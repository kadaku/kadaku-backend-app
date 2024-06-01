<?php
return [
  'key_auth' => base64_encode(env('SECRET_KEY_XENDIT') . ':'),
  'token_auth' => env('TOKEN_XENDIT')
];
