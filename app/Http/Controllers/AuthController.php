<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
 public function register(Request $request)
 {
  $validator = Validator::make($request->all(), [
   'nama' => 'required',
   'email' => 'required|email|unique:user,email',
   'password' => 'required|min:6',
   'confirmation_password' => 'required|same:password',
  ]);

  if ($validator->fails()) {
   return messageError($validator->messages()->toArray());
  }

  $user = $validator->validated();

  User::create($user);

  $payload = [
   'nama' => $user['nama'],
   'role' => 'user',
   'iat' => now()->timestamp,
   'exp' => now()->timestamp + 7200,
  ];

  $token = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');

  Log::create([
   'module' => 'Login',
   'action' => 'Login Akun',
   'useraccess' => $user['email'],
  ]);

  return response()->json([
   'data' => [
    'msg' => 'Berhasil Login',
    'nama' => $user['nama'],
    'email' => $user['email'],
    'role' => 'user',
   ],
   'token' => 'Bearer ' . $token
  ], 200);
 }

 public function login(Request $request)
 {
  $validator = Validator::make($request->all(), [
   'email' => 'required|email',
   'password' => 'required',
  ]);

  if ($validator->fails()) {

   return messageError($validator->messages()->toArray());
  }

  if (Auth::attempt($validator->validated())) {

   $payload = [
    'nama' => Auth::user()->nama,
    'role' => Auth::user()->role,
    'iat' => now()->timestamp,
    'exp' => now()->timestamp + 7200,
   ];

   $token = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');

   Log::create([
    'module' => 'Login',
    'action' => 'Login Akun',
    'useraccess' => Auth::user()->email,
   ]);

   return response()->json([
    'data' => [
     'msg' => 'Berhasil Login',
     'nama' => Auth::user()->nama,
     'email' => Auth::user()->email,
     'role' => Auth::user()->role,
    ],
    'token' => 'Bearer ' . $token
   ], 200);
  }

  return response()->json('Email Atau Password Salah', 422);
 }
}
