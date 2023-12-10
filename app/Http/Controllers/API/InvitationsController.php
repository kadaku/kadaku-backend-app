<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AuthModel;
use App\Models\API\InvitationsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class InvitationsController extends Controller
{
    function check_available_domain(Request $request) {
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
        $check_domain = DB::table('t_invitations')
            ->where('domain', $domain)
            ->where('customer_id', '!=', Auth::user()->id)
            ->count();

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

        if (empty($request->theme_id)) {
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
                                'props' => $value->props,
                                'icon' => $value->icon,
                                'is_icon' => $value->is_icon,
                                'thumbnail' => $value->thumbnail,
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
                        'message' => 'Successfully created an invitation card',
                        'data' => [
                            'invitation_id' => $invitation->id,
                        ]
                    ], 200);
                } else {
                    return response()->json([
                        'code' => 400,
                        'status' => false,
                        'message' => 'Failed to create invitation card',
                    ], 200);
                }
            } else {
                return response()->json([
                    'code' => 400,
                    'status' => false,
                    'message' => 'Forbidden Access',
                ], 200);
            }
        }
    }

    function list(Request $request)
    {
        $data_customer = Auth::user();
        $data = InvitationsModel::where('customer_id', $data_customer->id)
            ->when($request->q, fn ($query, $search) => $query->where('heading', 'like', '%' . $search . '%'))
            ->orderBy('id', 'desc')
            ->paginate(20);
        if ($data) {
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
        $data_customer = Auth::user();
        $data = InvitationsModel::where('id', '=', $id)->where('customer_id', $data_customer->id)->first();
        if ($data) {
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

        $update_invitation = DB::table('t_invitations')
            ->where('id', $payload['id'])
            ->where('customer_id', Auth::user()->id)
            ->update([
                "category_id" => $payload['category_id'],
                "theme_id" => $payload['theme_id'],
                "domain" => $payload['domain'],
                // "cover" => $payload['cover'] ?? NULL,
                "heading" => $payload['heading'] ?? NULL,
                "introduction" => $payload['introduction'] ?? NULL,
                // "first_event_date" => $payload['first_event_date'] ?? NULL,
                // "first_event_time" => $payload['first_event_time'] ?? NULL,
                // "first_event_gmt" => $payload['first_event_gmt'] ?? NULL,
                // "first_event_address" => $payload['first_event_address'] ?? NULL,
                // "music" => $payload['music'] ?? NULL,
                // "music_embed" => $payload['music_embed'] ?? NULL,
                // "is_music_status" => $payload['is_music_status'] ?? 1,
                // "is_music_type" => $payload['is_music_type'] ?? 0,
                "is_active" => $payload['is_active'] ?? 1,
                "is_favorite" => $payload['is_favorite'] ?? 0,
                "is_portofolio" => $payload['is_portofolio'] ?? 1,
                "is_watermark" => $payload['is_watermark'] ?? 1,
                "is_comment_form" => $payload['is_comment_form'] ?? 1,
                // "photo" => $payload['photo'] ?? NULL,
                // "partner_photo" => $payload['partner_photo'] ?? NULL,
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
            $image_type = $image_type_aux[1];
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
}
