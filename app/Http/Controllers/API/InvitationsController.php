<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\API\InvitationsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class InvitationsController extends Controller
{
	protected $path_cover;
	protected $path_background_custom;
	protected $path_background_screen_guests;
	
	function __construct()
	{
		parent::__construct();
		$this->path_cover = 'images/invitations/covers/';	
		$this->path_cover = 'images/invitations/covers/';	
		$this->path_background_custom = 'images/invitations/background/customs/';	
		$this->path_background_screen_guests = 'images/invitations/background/screen-guests/';	
	}
	
	function check_available_domain(Request $request)
	{
		// Trim and sanitize the domain input
		$domain = str_replace(' ', '', trim($request->domain));

		// Use the regular expression for validation
		$pattern = '/^(?!.*[^a-z0-9-_])[\w-]{3,100}$/';

		if (!preg_match($pattern, $domain)) {
			// If the domain does not match the pattern
			return response()->json([
				'code' => 400, // Bad Request
				'status' => false,
				'message' => 'Invalid domain format',
			], 400);
		}

		// Check if the domain is already in use
		$check_domain = DB::table('t_invitations')->where('domain', $domain)->where('id', '!=', $request->invitation_id)->count();
		if ($check_domain > 0) {
			// If the domain is already in use
			return response()->json([
				'code' => 406,
				'status' => false,
				'message' => 'The domain is already in use, please find another domain',
			], 406);
		} else {
			// If the domain is available
			return response()->json([
				'code' => 200,
				'status' => true,
				'message' => 'Domain is available',
			], 200);
		}
	}

	function create(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'domain' => 'required|string|max:100',
			'category_id' => 'required|int',
		]);

		if ($validator->fails()) {
			return response()->json([
				'code' => 200,
				'status' => false,
				'data' => $validator->errors(),
			], 200);
		}

		$valid_create = true;

		if (!isset($_POST['theme_id']) || empty($request->theme_id)) {
			$valid_create = false;
			$response = [
				'code' => 400,
				'status' => false,
				'message' => 'Please select a theme first',
			];
			return response()->json($response, 200);
		}

		// cek domain available
		$domain = str_replace(' ', '', trim($request->domain));
		$check_domain = DB::table('t_invitations')->where('domain', $domain)->count();
		if ($check_domain > 0) {
			// if domain is used
			return response()->json([
				'code' => 200,
				'status' => false,
				'data' => [
					'domain' => ['The domain is already in use, please find another domain'],
				],
			], 200);
		} else {
			if ($valid_create) {
				// if not used
				// data user login
				$data_customer = Auth::user();
				if ($data_customer) {
					// check if trial and not premium account just can create one invitation
					//!! not yet
	
					$customer_id = $data_customer->id;
					$category_id = $request->category_id;
					$theme_id = $request->theme_id;
	
					$data_categories = DB::table('m_categories')->where('id', '=', $category_id)->first();
	
					$data = [
						'customer_id' => $customer_id,
						'category_id' => $category_id,
						'theme_id' => $theme_id,
						'domain' => $domain,
						'heading' => $data_categories->meta_title,
						'introduction' => $data_categories->meta_description,
					];
	
					$invitation = InvitationsModel::create($data);
					if ($invitation) {
						$theme_component = DB::table('m_theme_components')->where('theme_id', $theme_id)->get();
						if ($theme_component) {
							$data_theme_component = [];
							foreach ($theme_component as $key => $value) {
								$data_theme_component[] = [
									'theme_id' => $theme_id,
									'invitation_id' => $invitation->id,
									'customer_id' => $customer_id,
									'name' => $value->name,
									'type' => $value->type,
									'ref' => $value->ref,
									'order' => $value->order,
									'styles_custom' => $value->styles_custom,
									'body' => $value->body,
									'props' => $value->props,
									'icon' => $value->icon,
									'is_icon' => $value->is_icon,
									'background' => $value->background,
									'is_premium' => $value->is_premium,
									'is_active' => $value->is_active,
									'is_fixed' => $value->is_fixed,
									'is_always_on' => $value->is_always_on,
								];
							}
							DB::table('t_theme_components')->insert($data_theme_component);
						}
	
						return response()->json([
							'code' => 200,
							'status' => true,
							'message' => 'Successfully Created an Invitation Card',
							'data' => [
								'invitation_id' => $invitation->id,
							]
						], 200);
					} else {
						return response()->json([
							'code' => 400,
							'status' => false,
							'message' => 'Failed to Ceate Invitation Card',
						], 200);
					}
				} else {
					return response()->json([
						'code' => 400,
						'status' => false,
						'message' => 'Forbidden Access',
					], 200);
				}
			} else {
				return response()->json([
					'code' => 400,
					'status' => false,
					'message' => 'Not Valid to Create',
				], 200);
			}
		}
	}

	function list(Request $request)
	{
		$data_customer = Auth::user();
		$data = InvitationsModel::where('customer_id', $data_customer->id)
			->when($request->q, fn ($query, $search) => $query->where('heading', 'like', '%' . $search . '%')->orWhere('domain', 'like', '%' . $search . '%'))
			// ->orderBy('is_active', 'desc')
			->orderBy('id', 'desc')
			->paginate(20);
		if ($data) {
			foreach ($data as $i => $value) {
				$path_thumbnail = 'images/themes/thumbnails/';
				$theme = DB::table('m_themes')->select('thumbnail_xs')->where('id', '=', $value->theme_id)->first();
				$data[$i]->thumbnail = '';
				if ($theme->thumbnail_xs && Storage::disk('public')->exists($path_thumbnail . $theme->thumbnail_xs)) {
					$data[$i]->thumbnail = asset('storage/' . $path_thumbnail . $theme->thumbnail_xs);
				}

				if ($value->cover && Storage::disk('public')->exists($this->path_cover.$value->cover)) {
					$data[$i]->cover = asset('storage/'.$this->path_cover.$value->cover);
				}
				if ($value->background_custom && Storage::disk('public')->exists($this->path_background_custom.$value->background_custom)) {
					$data[$i]->background_custom = asset('storage/'.$this->path_background_custom.$value->background_custom);
				}
				if ($value->background_screen_guests && Storage::disk('public')->exists($this->path_background_screen_guests.$value->background_screen_guests)) {
					$data[$i]->background_screen_guests = asset('storage/'.$this->path_background_screen_guests.$value->background_screen_guests);
				}
			}

			return response()->json([
				'code' => 200,
				'status' => true,
				'message' => 'Data Found',
				'data' => $data,
			], 200);
		} else {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Data Not Found',
			], 200);
		}
	}

	function get($id)
	{
		if (isset($id) && empty($id)) {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Bad Request',
			], 200);
		}

		$data_customer = Auth::user();
		$data = InvitationsModel::where('id', '=', $id)->where('customer_id', $data_customer->id)->first();
		if ($data) {
			if ($data->is_active == 0) {
				return response()->json([
					'code' => 400,
					'status' => false,
					'message' => 'Your Invitation Card Is Not Active',
					'data' => [],
				], 200);
			}

			if ($data->cover && Storage::disk('public')->exists($this->path_cover.$data->cover)) {
				$data->cover = asset('storage/'.$this->path_cover.$data->cover);
			}
			if ($data->background_custom && Storage::disk('public')->exists($this->path_background_custom.$data->background_custom)) {
				$data->background_custom = asset('storage/'.$this->path_background_custom.$data->background_custom);
			}
			if ($data->background_screen_guests && Storage::disk('public')->exists($this->path_background_screen_guests.$data->background_screen_guests)) {
				$data->background_screen_guests = asset('storage/'.$this->path_background_screen_guests.$data->background_screen_guests);
			}

			$data_theme = DB::table('m_themes as t')
				->leftJoin('m_categories as c', 'c.id', 't.category_id')
				->leftJoin('m_themes_type as tt', 'tt.id', 't.type_id')
				->select([
					't.id AS theme_id',
					't.category_id AS theme_category_id',
					't.type_id AS theme_type_id',
					't.name AS theme_name',
					't.slug AS theme_slug',
					't.layout AS theme_layout',
					't.description AS theme_description',
					't.background AS theme_background',
					't.thumbnail AS theme_thumbnail',
					't.thumbnail_xs AS theme_thumbnail_xs',
					't.price AS theme_price',
					't.discount AS theme_discount',
					't.is_premium AS theme_is_premium',
					't.styles AS theme_styles',
					't.version AS theme_version',
					't.is_active AS theme_is_active',
					't.created_at AS theme_created_at',
					't.updated_at AS theme_updated_at',
					'c.id AS category_id',
					'c.slug AS category_slug',
					'c.icon AS category_icon',
					'c.meta_title AS category_meta_title',
					'c.meta_description AS category_meta_description',
					'c.name AS category_name',
					'c.is_active AS category_is_active',
					'c.created_at AS category_created_at',
					'c.updated_at AS category_updated_at',
					'tt.id AS theme_type_id',
					'tt.name AS theme_type_name',
					'tt.is_active AS theme_type_is_active',
					'tt.created_at AS theme_type_created_at',
					'tt.updated_at AS theme_type_updated_at'
				])
				->where('t.id', $data->theme_id)
				->first();
			$data->theme = $data_theme;

			$data_theme_component = DB::table('t_theme_components as tc')
				->select([
					'tc.*',
					DB::raw('(ROW_NUMBER() OVER ( PARTITION BY tc.name ORDER BY tc.order )) AS row_num')
				])
				->where('tc.theme_id', $data_theme->theme_id)
				->where('tc.invitation_id', $id)
				->where('tc.customer_id', $data_customer->id)
				->where('tc.is_active', 1)
				->orderBy('tc.order', 'asc')
				->get();
			$data->theme->components = $data_theme_component;

			return response()->json([
				'code' => 200,
				'status' => true,
				'message' => 'Data Found',
				'data' => $data,
			], 200);
		} else {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Data Not Found',
			], 400);
		}
	}

	function get_all($id)
	{
		$data_customer = Auth::user();
		$data = InvitationsModel::where('id', '=', $id)->where('customer_id', $data_customer->id)->first();
		if ($data) {
			if ($data->cover && Storage::disk('public')->exists($this->path_cover.$data->cover)) {
				$data->cover = asset('storage/'.$this->path_cover.$data->cover);
			}
			if ($data->background_custom && Storage::disk('public')->exists($this->path_background_custom.$data->background_custom)) {
				$data->background_custom = asset('storage/'.$this->path_background_custom.$data->background_custom);
			}
			if ($data->background_screen_guests && Storage::disk('public')->exists($this->path_background_screen_guests.$data->background_screen_guests)) {
				$data->background_screen_guests = asset('storage/'.$this->path_background_screen_guests.$data->background_screen_guests);
			}
			
			$data_theme = DB::table('m_themes as t')
				->leftJoin('m_categories as c', 'c.id', 't.category_id')
				->leftJoin('m_themes_type as tt', 'tt.id', 't.type_id')
				->select([
					't.id AS theme_id',
					't.category_id AS theme_category_id',
					't.type_id AS theme_type_id',
					't.name AS theme_name',
					't.slug AS theme_slug',
					't.layout AS theme_layout',
					't.description AS theme_description',
					't.background AS theme_background',
					't.thumbnail AS theme_thumbnail',
					't.thumbnail_xs AS theme_thumbnail_xs',
					't.price AS theme_price',
					't.discount AS theme_discount',
					't.is_premium AS theme_is_premium',
					't.styles AS theme_styles',
					't.version AS theme_version',
					't.is_active AS theme_is_active',
					't.created_at AS theme_created_at',
					't.updated_at AS theme_updated_at',
					'c.id AS category_id',
					'c.slug AS category_slug',
					'c.icon AS category_icon',
					'c.meta_title AS category_meta_title',
					'c.meta_description AS category_meta_description',
					'c.name AS category_name',
					'c.is_active AS category_is_active',
					'c.created_at AS category_created_at',
					'c.updated_at AS category_updated_at',
					'tt.id AS theme_type_id',
					'tt.name AS theme_type_name',
					'tt.is_active AS theme_type_is_active',
					'tt.created_at AS theme_type_created_at',
					'tt.updated_at AS theme_type_updated_at'
				])
				->where('t.id', $data->theme_id)
				->first();
			$data->theme = $data_theme;

			$data_theme_component = DB::table('t_theme_components as tc')
				->select([
					'tc.*',
					DB::raw('(ROW_NUMBER() OVER ( PARTITION BY tc.name ORDER BY tc.order )) AS row_num')
				])
				->where('tc.theme_id', $data_theme->theme_id)
				->where('tc.invitation_id', $id)
				->where('tc.customer_id', $data_customer->id)
				// except type extras and welcome, footer for edit
				->where('tc.type', '!=', 'extras')
				->where(DB::raw('LOWER(tc.name)'), '!=', 'welcome')
				->where(DB::raw('LOWER(tc.name)'), '!=', 'footer')
				->orderBy('tc.order', 'asc')
				->get();
			$data->theme->components = $data_theme_component;

			return response()->json([
				'code' => 200,
				'status' => true,
				'message' => 'Data Found',
				'data' => $data,
			], 200);
		} else {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Data Not Found',
			], 400);
		}
	}

	function get_by_domain($domain = '')
	{
		if (isset($domain) && empty($domain)) {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Bad Request',
			], 200);
		}
		$data = InvitationsModel::where('domain', '=', $domain)->first();
		if ($data) {
			if ($data->is_active == 0) {
				return response()->json([
					'code' => 400,
					'status' => false,
					'message' => 'Your Invitation Card Is Not Active',
					'data' => [],
				], 200);
			}

			if ($data->cover && Storage::disk('public')->exists($this->path_cover.$data->cover)) {
				$data->cover = asset('storage/'.$this->path_cover.$data->cover);
			}
			if ($data->background_custom && Storage::disk('public')->exists($this->path_background_custom.$data->background_custom)) {
				$data->background_custom = asset('storage/'.$this->path_background_custom.$data->background_custom);
			}
			if ($data->background_screen_guests && Storage::disk('public')->exists($this->path_background_screen_guests.$data->background_screen_guests)) {
				$data->background_screen_guests = asset('storage/'.$this->path_background_screen_guests.$data->background_screen_guests);
			}

			$data_theme = DB::table('m_themes as t')
				->leftJoin('m_categories as c', 'c.id', 't.category_id')
				->leftJoin('m_themes_type as tt', 'tt.id', 't.type_id')
				->select([
					't.id as theme_id',
					't.category_id as theme_category_id',
					't.type_id as theme_type_id',
					't.name as theme_name',
					't.slug as theme_slug',
					't.layout as theme_layout',
					't.description as theme_description',
					't.background as theme_background',
					't.thumbnail as theme_thumbnail',
					't.thumbnail_xs as theme_thumbnail_xs',
					't.price as theme_price',
					't.discount as theme_discount',
					't.is_premium as theme_is_premium',
					't.styles as theme_styles',
					't.version as theme_version',
					't.is_active as theme_is_active',
					't.created_at as theme_created_at',
					't.updated_at as theme_updated_at',
					'c.id as category_id',
					'c.slug as category_slug',
					'c.icon as category_icon',
					'c.meta_title as category_meta_title',
					'c.meta_description as category_meta_description',
					'c.name as category_name',
					'c.is_active as category_is_active',
					'c.created_at as category_created_at',
					'c.updated_at as category_updated_at',
					'tt.id as theme_type_id',
					'tt.name as theme_type_name',
					'tt.is_active as theme_type_is_active',
					'tt.created_at as theme_type_created_at',
					'tt.updated_at as theme_type_updated_at'
				])
				->where('t.id', $data->theme_id)
				->first();
				
			$data->theme = $data_theme;

			$data_theme_component = DB::table('t_theme_components as tc')
				->select([
					'tc.*',
					DB::raw('(ROW_NUMBER() OVER ( PARTITION BY tc.name ORDER BY tc.order )) AS row_num')
				])
				->where('tc.theme_id', $data_theme->theme_id)
				->where('tc.invitation_id', $data->id)
				->where('tc.is_active', 1)
				->orderBy('tc.order', 'asc')
				->get();
			$data->theme->components = $data_theme_component;

			return response()->json([
				'code' => 200,
				'status' => true,
				'message' => 'Data Found',
				'data' => $data,
			], 200);
		} else {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Data Not Found',
			], 400);
		}
	}

	function get_by_domain_v2($domain = '')
	{
		if (isset($domain) && empty($domain)) {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Bad Request',
			], 200);
		}
		$data = InvitationsModel::where('domain', '=', $domain)->first();
		if ($data) {
			if ($data->is_active == 0) {
				return response()->json([
					'code' => 400,
					'status' => false,
					'message' => 'Your Invitation Card Is Not Active',
					'data' => [],
				], 200);
			}

			if ($data->cover && Storage::disk('public')->exists($this->path_cover.$data->cover)) {
				$data->cover = asset('storage/'.$this->path_cover.$data->cover);
			}
			if ($data->background_custom && Storage::disk('public')->exists($this->path_background_custom.$data->background_custom)) {
				$data->background_custom = asset('storage/'.$this->path_background_custom.$data->background_custom);
			}
			if ($data->background_screen_guests && Storage::disk('public')->exists($this->path_background_screen_guests.$data->background_screen_guests)) {
				$data->background_screen_guests = asset('storage/'.$this->path_background_screen_guests.$data->background_screen_guests);
			}

			$data_theme = DB::table('m_themes as t')
				->leftJoin('m_categories as c', 'c.id', 't.category_id')
				->leftJoin('m_themes_type as tt', 'tt.id', 't.type_id')
				->select([
					't.id as theme_id',
					't.category_id as theme_category_id',
					't.type_id as theme_type_id',
					't.name as theme_name',
					't.slug as theme_slug',
					't.layout as theme_layout',
					't.description as theme_description',
					't.thumbnail as theme_thumbnail',
					't.thumbnail_xs as theme_thumbnail_xs',
					't.background as theme_background',
					't.is_premium as theme_is_premium',
					't.version as theme_version',
					't.is_active as theme_is_active',
					't.created_at as theme_created_at',
					't.updated_at as theme_updated_at',
					'c.id as category_id',
					'c.slug as category_slug',
					'c.icon as category_icon',
					'c.meta_title as category_meta_title',
					'c.meta_description as category_meta_description',
					'c.name as category_name',
					'c.is_active as category_is_active',
					'c.created_at as category_created_at',
					'c.updated_at as category_updated_at',
					'tt.id as theme_type_id',
					'tt.name as theme_type_name',
					'tt.is_active as theme_type_is_active',
					'tt.created_at as theme_type_created_at',
					'tt.updated_at as theme_type_updated_at'
				])
				->where('t.id', $data->theme_id)
				->first();
				
			$data->theme = $data_theme;

			$data_theme_component = DB::table('t_theme_components as tc')
				->select([
					'tc.*',
					DB::raw('(ROW_NUMBER() OVER ( PARTITION BY tc.name ORDER BY tc.order )) AS row_num')
				])
				->where('tc.theme_id', $data_theme->theme_id)
				->where('tc.invitation_id', $data->id)
				->where('tc.is_active', 1)
				->orderBy('tc.order', 'asc')
				->get();
			$data->theme->components = $data_theme_component;

			return response()->json([
				'code' => 200,
				'status' => true,
				'message' => 'Data Found',
				'data' => $data,
			], 200);
		} else {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Data Not Found',
			], 400);
		}
	}

	function get_wishes_by_domain($slug = '', $invitation_id = 0, $per_fetch = 20, $current_total = 0)
	{
		$row_per_fetch = $per_fetch;
		$current_total = $current_total;

		$data = InvitationsModel::where('domain', '=', $slug)->first();
		if ($data) {
			$query = DB::table('t_wishes')
				->select('*')
				->where('invitation_id', $invitation_id);

			$total_messages = $query->count();
			$skip_to_newest = ($total_messages - $current_total) - $row_per_fetch;
			$slice_index = $skip_to_newest < 0 ? 0 : $skip_to_newest;
			$slice_take = $skip_to_newest < 0 ? $skip_to_newest + $row_per_fetch : $row_per_fetch;

			$wishes = $query->skip($slice_index)->take($slice_take)->orderBy('created_at', 'desc')->get();
			$wishes = $query->orderBy('created_at', 'desc')->get();

			if ($wishes) {
				if ($current_total >= $total_messages) {
					return response()->json([
						'code' => 202,
						'status' => false,
						'message' => 'You have successfully retrieved all the messages',
					], 202);
				} else {
					return response()->json([
						'code' => 200,
						'status' => true,
						'message' => 'Messages captured',
						'data' => [
							'messages' => $wishes,
							'messages_total' => $total_messages,
							'current_total' => $current_total + $row_per_fetch
						]
					], 200);
				}
			}
		} else {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Data Not Found',
			], 400);
		}
	}

	function store_wish_by_domain(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'domain' => 'required',
			'customer_id' => 'required|numeric',
			'invitation_id' => 'required|numeric',
			'name' => 'required',
			'wish' => 'required',
		]);

		if ($validator->fails()) :
			return response()->json([
				"status" => false,
				"message" => 'Invalid message payload',
				"data" => $validator->errors()
			], 406);
		endif;

		$data = InvitationsModel::where('domain', '=', $request->domain)->first();
		if ($data) {
			$store_wish = DB::table('t_wishes')->insert([
				"customer_id" => $request->customer_id,
				"invitation_id" => $request->invitation_id,
				"name" => $request->name,
				"message" => $request->wish,
			]);

			if ($store_wish) {
				return response()->json([
					'code' => 200,
					'status' => true,
					'message' => 'Message Sent'
				], 200);
			}
		} else {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Data Not Found',
			], 400);
		}
	}

	function update(Request $request)
	{
		$payload = $request->all();
		// check domain
		if (!empty($payload['domain'])) {
			$check_domain = DB::table('t_invitations')->where('domain', $payload['domain'])->where('id', '!=' ,$payload['id'])->first();
			if ($check_domain) {
				return response()->json([
					'code' => 400,
					'status' => false,
					'message' => 'Domain not available, please try another one',
					'data' => $payload,
					'affected_components' => 0
				], 200);	
			}
		} else {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Domain cannot be empty',
				'data' => $payload,
				'affected_components' => 0
			], 200);
		}

		// check music from source
		if (!empty($payload['music_embed'])) {
			$allowed_extensions = ['mp3', 'wav', 'aac'];
			$extension = pathinfo(parse_url($payload['music_embed'], PHP_URL_PATH), PATHINFO_EXTENSION);
			if (!in_array(strtolower($extension), $allowed_extensions)) {
				return response()->json([
					'code' => 400,
					'status' => false,
					'message' => 'Source Music extention not allowed',
					'data' => $payload,
					'affected_components' => 0
				], 200);
			}
		}

		// cek apakah ada update tema atau tidak
		// !! belum

		$styles = NULL;
		if (isset($payload['styles']) && $payload['styles']) {
			$styles = [
				'primary' => $payload['styles']['colors']['primary'],
				'tertiary' => $payload['styles']['colors']['tertiary'],
				'secondary' => $payload['styles']['colors']['secondary'],
				'quaternary' => $payload['styles']['colors']['quaternary']
			];
			$styles = json_encode(['colors' => $styles]);
		}

		$update_invitation = DB::table('t_invitations')
			->where('id', $payload['id'])
			->where('customer_id', Auth::user()->id)
			->update([
				"category_id" => $payload['category_id'],
				"theme_id" => $payload['theme_id'],
				"domain" => $payload['domain'],
				"heading" => $payload['heading'] ?? NULL,
				"introduction" => $payload['introduction'] ?? NULL,
				"styles" => $styles,
				"first_event_datetime" => isset($payload['first_event_datetime']) && !empty($payload['first_event_datetime']) ? convert_timestamp_to_server($payload['first_event_datetime']) : NULL,
				"first_event_gmt" => $payload['first_event_gmt'] ?? NULL,
				"first_event_address" => $payload['first_event_address'] ?? NULL,
				"music_id" => $payload['music_id'] ?? NULL,
				"music" => $payload['music'] ?? NULL,
				"music_embed" => $payload['music_embed'] ?? NULL,
				"is_music_status" => $payload['is_music_status'] ?? 1,
				"is_music_type" => $payload['is_music_type'] ?? 0,
				"is_active" => $payload['is_active'] ?? 1,
				"is_favorite" => $payload['is_favorite'] ?? 0,
				"is_portofolio" => $payload['is_portofolio'] ?? 1,
				"is_watermark" => $payload['is_watermark'] ?? 1,
				"is_comment_form" => $payload['is_comment_form'] ?? 1,
				"version" => $payload['version'] ?? 1
			]);

		$processed_components = 0;
		foreach ($payload['theme']['components'] as $key => $row) {
			$update_components = DB::table('t_theme_components')
				->where('id', $row['id'])
				->where('theme_id', $row['theme_id'])
				->where('invitation_id', $row['invitation_id'])
				->where('customer_id', Auth::user()->id)
				->update([
					"name" => $row['name'],
					"type" => $row['type'],
					"ref" => $row['ref'],
					"order" => (int) $row['order'],
					"props" => isset($row['props']) ? json_encode($row['props']) : json_encode([]),
					"icon" => $row['icon'] ?? NULL,
					"is_icon" => (int) $row['is_icon'] ?? 0,
					"thumbnail" => $row['thumbnail'] ?? NULL,
					"is_premium" => (int) $row['is_premium'] ?? 0,
					"is_active" => (int) $row['is_active'] ?? 1,
					"is_fixed" => (int) $row['is_fixed'] ?? 0,
					"is_always_on" => (int) $row['is_always_on'] ?? 0
				]);

			if ($update_components) {
				$processed_components++;
			}
		}

		if ($update_invitation || $processed_components > 0) {
			return response()->json([
				'code' => 200,
				'status' => true,
				'message' => 'Data updated',
				'data' => $payload,
				'affected_components' => $processed_components
			], 200);
		} else {
			return response()->json([
				'code' => 200,
				'status' => true,
				'message' => 'No Data to be updated',
				'data' => $payload,
				'affected_components' => $processed_components
			], 200);
		}
	}

	function store_image(Request $request)
	{
		$data = Auth::user();

		if (DB::table('t_theme_components')->where('id', $request->formId)->where('customer_id', $data->id)->exists()) {
			$contextAbbr = substr($request->context, 0, 3);

			$currentDomain = request()->getSchemeAndHttpHost();
			$prefixToRemove = $currentDomain . '/storage/';

			Storage::delete('public/' . str_replace($prefixToRemove, '', $request->old_photo));
			$path = public_path('storage/images/invitations/' . $request->context);
			!is_dir($path) &&
				mkdir($path, 0777, true);

			$image_parts = explode(";base64,", $request->photo);
			$image_type_aux = explode("image/", $image_parts[0]);
			// $image_type = $image_type_aux[1];
			$image_type = 'webp';
			$image_base64 = base64_decode($image_parts[1]);
			$image_name = 'inv-' . $contextAbbr . '-' . $request->id . '-' . time() . '-' . sha1($data->name);
			$image_full_path = $path . '/' . $image_name . '.' . $image_type;
			file_put_contents($image_full_path, $image_base64);

			return response()->json([
				'code' => 200,
				'status' => true,
				'message' => 'Success create image',
				'data' => [
					'photo' => asset('storage/images/invitations/' . $request->context . '/' . $image_name . '.' . $image_type)
				]
			], 200);
		} else {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Not authorized to store image'
			], 400);
		}
	}

	function destroy_image(Request $request)
	{
		$data = Auth::user();

		if (DB::table('t_theme_components')->where('id', $request->formId)->where('customer_id', $data->id)->exists()) {
			$contextAbbr = substr($request->context, 0, 3);

			$currentDomain = request()->getSchemeAndHttpHost();
			$prefixToRemove = $currentDomain . '/storage/';

			Storage::delete('public/' . str_replace($prefixToRemove, '', $request->src));

			return response()->json([
				'code' => 200,
				'status' => true,
				'message' => 'Success destroy ' . $contextAbbr . ' image'
			], 200);
		} else {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Not authorized to destroy image'
			], 400);
		}
	}

	function store_cover(Request $request)
	{
		$update = false;
		$url_cover = null;
		if (DB::table('t_invitations')->where('id', $request->invitation_id)->exists()) {
			// upload photo
			if ($request->file('cover')) {
				// remove the old avatar if it exists
				$file_old = DB::table('t_invitations')->select(['cover'])->where('id', $request->invitation_id)->first();
				if ($file_old && Storage::disk('public')->exists($this->path_cover.$file_old->cover)) {
					Storage::disk('public')->delete($this->path_cover.$file_old->cover);
				}
				$file = $request->file('cover');
				$file_ext = 'webp';
				$file_name = 'inv-cov-'.$request->invitation_id.'-'.time().'-'.sha1($request->invitation_id);
				$file_name_fix = $file_name.'.'.$file_ext;
				$webp_image = $this->convert_to_webp($file->getPathname());
				
				Storage::disk('public')->put($this->path_cover.$file_name_fix, $webp_image);
				$data['cover'] = $file_name_fix;
				$update = DB::table('t_invitations')
				->where('id', $request->invitation_id)
				->where('customer_id', Auth::user()->id)
				->update($data);
			
				if (Storage::disk('public')->exists($this->path_cover.$file_name_fix)) {
					$url_cover = asset('storage/'.$this->path_cover.$file_name_fix);
				}
			}
			// end upload photo

			if ($update) {
				return response()->json([
					'code' => 200,
					'status' => true,
					'message' => 'Successfully Upload Cover',
					'data' => [
						'cover' => $url_cover,
					]
				], 200);
			} else {
				return response()->json([
					'code' => 400,
					'status' => false,
					'message' => 'Failed Upload Cover'
				], 400);	
			}
		} else {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Not Authorized to Store Cover'
			], 400);
		}
	}

	function store_background_custom(Request $request)
	{
		$update = false;
		$url_background_custom = null;
		if (DB::table('t_invitations')->where('id', $request->invitation_id)->exists()) {
			// upload photo
			if ($request->file('background_custom')) {
				// remove the old avatar if it exists
				$file_old = DB::table('t_invitations')->select(['background_custom'])->where('id', $request->invitation_id)->first();
				if ($file_old && Storage::disk('public')->exists($this->path_background_custom.$file_old->background_custom)) {
					Storage::disk('public')->delete($this->path_background_custom.$file_old->background_custom);
				}
				$file = $request->file('background_custom');
				$file_ext = 'webp';
				$file_name = 'inv-bg-custom-'.$request->invitation_id.'-'.time().'-'.sha1($request->invitation_id);
				$file_name_fix = $file_name.'.'.$file_ext;
				$webp_image = $this->convert_to_webp($file->getPathname());
				
				Storage::disk('public')->put($this->path_background_custom.$file_name_fix, $webp_image);
				$data['background_custom'] = $file_name_fix;
				$update = DB::table('t_invitations')
				->where('id', $request->invitation_id)
				->where('customer_id', Auth::user()->id)
				->update($data);
			
				if (Storage::disk('public')->exists($this->path_background_custom.$file_name_fix)) {
					$url_background_custom = asset('storage/'.$this->path_background_custom.$file_name_fix);
				}
			}
			// end upload photo

			if ($update) {
				return response()->json([
					'code' => 200,
					'status' => true,
					'message' => 'Successfully Upload Background',
					'data' => [
						'background_custom' => $url_background_custom,
					]
				], 200);
			} else {
				return response()->json([
					'code' => 400,
					'status' => false,
					'message' => 'Failed Upload Background'
				], 400);	
			}
		} else {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Not Authorized to Store Background'
			], 400);
		}
	}

	function destroy_background_custom(Request $request)
	{
		$update = false;
		if (DB::table('t_invitations')->where('id', $request->invitation_id)->exists()) {
			$file_old = DB::table('t_invitations')->select(['background_custom'])->where('id', $request->invitation_id)->first();

			$update = DB::table('t_invitations')
				->where('id', $request->invitation_id)
				->where('customer_id', Auth::user()->id)
				->update(['background_custom' => NULL]);

			if ($file_old && Storage::disk('public')->exists($this->path_background_custom.$file_old->background_custom)) {
				Storage::disk('public')->delete($this->path_background_custom.$file_old->background_custom);
			}

			if ($update) {
				return response()->json([
					'code' => 200,
					'status' => true,
					'message' => 'Successfully Destroy Background',
				], 200);
			} else {
				return response()->json([
					'code' => 400,
					'status' => false,
					'message' => 'Failed Destroy Background'
				], 400);	
			}
		} else {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Not Authorized to Destroy Background'
			], 400);
		}
	}

	function store_background_screen_guests(Request $request)
	{
		$update = false;
		$url_background_screen_guests = null;
		if (DB::table('t_invitations')->where('id', $request->invitation_id)->exists()) {
			// upload photo
			if ($request->file('background_screen_guests')) {
				// remove the old avatar if it exists
				$file_old = DB::table('t_invitations')->select(['background_screen_guests'])->where('id', $request->invitation_id)->first();
				if ($file_old && Storage::disk('public')->exists($this->path_background_screen_guests.$file_old->background_screen_guests)) {
					Storage::disk('public')->delete($this->path_background_screen_guests.$file_old->background_screen_guests);
				}
				$file = $request->file('background_screen_guests');
				$file_ext = 'webp';
				$file_name = 'inv-bg-screen-guests-'.$request->invitation_id.'-'.time().'-'.sha1($request->invitation_id);
				$file_name_fix = $file_name.'.'.$file_ext;
				$webp_image = $this->convert_to_webp($file->getPathname());
				
				Storage::disk('public')->put($this->path_background_screen_guests.$file_name_fix, $webp_image);
				$data['background_screen_guests'] = $file_name_fix;
				$update = DB::table('t_invitations')
				->where('id', $request->invitation_id)
				->where('customer_id', Auth::user()->id)
				->update($data);
			
				if (Storage::disk('public')->exists($this->path_background_screen_guests.$file_name_fix)) {
					$url_background_screen_guests = asset('storage/'.$this->path_background_screen_guests.$file_name_fix);
				}
			}
			// end upload photo

			if ($update) {
				return response()->json([
					'code' => 200,
					'status' => true,
					'message' => 'Successfully Upload Background Screen Guets',
					'data' => [
						'background_screen_guests' => $url_background_screen_guests,
					]
				], 200);
			} else {
				return response()->json([
					'code' => 400,
					'status' => false,
					'message' => 'Failed Upload Background Screen Guets'
				], 400);	
			}
		} else {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Not Authorized to Store Background Screen Guets'
			], 400);
		}
	}

	function destroy_background_screen_guests(Request $request)
	{
		$update = false;
		if (DB::table('t_invitations')->where('id', $request->invitation_id)->exists()) {
			$file_old = DB::table('t_invitations')->select(['background_screen_guests'])->where('id', $request->invitation_id)->first();

			$update = DB::table('t_invitations')
				->where('id', $request->invitation_id)
				->where('customer_id', Auth::user()->id)
				->update(['background_screen_guests' => NULL]);

			if ($file_old && Storage::disk('public')->exists($this->path_background_screen_guests.$file_old->background_screen_guests)) {
				Storage::disk('public')->delete($this->path_background_screen_guests.$file_old->background_screen_guests);
			}

			if ($update) {
				return response()->json([
					'code' => 200,
					'status' => true,
					'message' => 'Successfully Destroy Background Screen Guets',
				], 200);
			} else {
				return response()->json([
					'code' => 400,
					'status' => false,
					'message' => 'Failed Destroy Background Screen Guets'
				], 400);	
			}
		} else {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Not Authorized to Destroy Background Screen Guets'
			], 400);
		}
	}
}
