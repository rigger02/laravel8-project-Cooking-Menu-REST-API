<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Tool;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
 public function register(Request $request)
 {
  $validator = Validator::make($request->all(), [
   'nama' => 'required',
   'email' => 'required|email|unique:user,email',
   'password' => 'required|min:6',
   'confirmation_password' => 'required|same:password',
   'role' => 'required|in:admin,user',
   'status' => 'required|in:aktif,non-aktif',
   'email_validate' => 'required|email',
  ]);

  if ($validator->fails()) {
   return messageError($validator->messages()->toArray());
  }

  $user = $validator->validated();

  User::create($user);

  return response()->json([
   'data' => [
    'msg' => 'Berhasil Login',
    'nama' => $user['nama'],
    'email' => $user['email'],
    'role' => $user['role'],
   ]
  ], 200);
 }

 public function show_register()
 {
  $users = User::where('role', 'user')->get();

  return response()->json([
   'data' => [
    'msg' => 'User Registrasi',
    'data' => $users,
   ]
  ], 200);
 }

 public function show_register_by_id($id)
 {
  $user = User::find($id);

  return response()->json([
   'data' => [
    'msg' => 'User ID : ' . $id,
    'data' => $user
   ]
  ], 200);
 }

 public function update_register(Request $request, $id)
 {
  $user = User::find($id);

  if ($user) {
   $validator = Validator::make($request->all(), [
    'nama' => 'required',
    'password' => 'min:6',
    'confirmation_password' => 'same:password',
    'role' => 'required|in:admin,user',
    'status' => 'required|in:aktif,non-aktif',
    'email_validate' => 'required|email',
   ]);

   if ($validator->fails()) {
    return messageError($validator->messages()->toArray());
   }

   $data = $validator->validated();

   User::where('id', $id)->update($data);

   return response()->json([
    'data' => [
     'msg' => 'User Dengan ID : ' . $id . ' Berhasil Di Update',
     'nama' => $data['nama'],
     'email' => $user['email'],
     'role' => $data['role'],
    ]
   ], 200);
  }

  return response()->json([
   'data' => [
    'msg' => 'User Dengan ID : ' . $id . ' Tidak Di Temukan'
   ]
  ], 422);
 }

 public function delete_register($id)
 {
  $user = User::find($id);

  if ($user) {

   $user->delete();

   return response()->json([
    'data' => [
     'msg' => 'User Dengan ID : ' . $id . ', Berhasil Di Hapus'
    ]
   ], 200);
  }

  return response()->json([
   'data' => [
    'msg' => 'User Dengan ID : ' . $id . ', Tidak Di Temukan'
   ]
  ], 422);
 }

 public function activation_account($id)
 {
  $user = User::find($id);

  if ($user) {
   User::where('id', $id)->update(['status' => 'aktif']);

   return response()->json([
    'data' => [
     'msg' => 'User Dengan ID : ' . $id . ', Berhasil Di Aktifkan'
    ]
   ], 200);
  }

  return response()->json([
   'data' => [
    'msg' => 'User Dengan ID : ' . $id . ', Tidak Di Teumakan'
   ]
  ], 422);
 }

 public function deactivation_account($id)
 {
  $user = User::find($id);

  if ($user) {
   User::where('id', $id)->update(['status' => 'non-aktif']);

   return response()->json([
    'data' => [
     'msg' => 'User Dengan ID : ' . $id . ', Berhasil Di Non Aktifkan'
    ]
   ], 200);
  }

  return response()->json([
   'data' => [
    'msg' => 'User Dengan ID : ' . $id . ', Tidak Di Teumakan'
   ]
  ], 422);
 }

 public function create_recipe(Request $request)
 {
  $validator = Validator::make($request->all(), [
   'judul' => 'required|max:255',
   'gambar' => 'required|mimes:png,jpg,jpeg|max:2048',
   'cara_pembuatan' => 'required',
   'video' => 'required',
   'user_email' => 'required',
   'bahan' => 'required',
   'alat' => 'required',
  ]);

  if ($validator->fails()) {
   return messageError($validator->messages()->toArray());
  }

  $thumbnail = $request->file('gambar');

  $fileName = now()->timestamp . '_' . $request->gambar->getClientOriginalName();

  $thumbnail->move('uploads', $fileName);

  $recipeData = $validator->validated();

  $recipe = Recipe::create([
   'judul' => $recipeData['judul'],
   'gambar' => 'uploads/' . $fileName,
   'cara_pembuatan' => $recipeData['cara_pembuatan'],
   'video' => $recipeData['video'],
   'user_email' => $recipeData['user_email'],
   'status_resep' => 'submit',
  ]);

  foreach (json_decode($request->bahan) as $bahan) {
   Ingredient::create([
    'nama' => $bahan->nama,
    'satuan' => $bahan->satuan,
    'banyak' => $bahan->banyak,
    'keterangan' => $bahan->keterangan,
    'resep_idresep' => $recipe->id,
   ]);
  }

  foreach (json_decode($request->alat) as $alat) {
   Tool::create([
    'nama_alat' => $alat->nama,
    'keterangan' => $alat->keterangan,
    'resep_idresep' => $recipe->id,
   ]);
  }

  return response()->json([
   'data' => [
    'msg' => 'Resep Berhasil Di Simpan',
    'resep' => $recipeData['judul'],
   ]
  ]);
 }

 public function update_recipe(Request $request, $id)
 {
  $validator = Validator::make($request->all(), [
   'judul' => 'required|max:255',
   'gambar' => 'required|mimes:png,jpg,jpeg|max:2048',
   'cara_pembuatan' => 'required',
   'video' => 'required',
   'user_email' => 'required',
   'bahan' => 'required',
   'alat' => 'required',
  ]);

  if ($validator->fails()) {
   return messageError($validator->messages()->toArray());
  }

  $thumbnail = $request->file('gambar');

  $fileName = now()->timestamp . '_' . $request->gambar->getClientOriginalName();

  $thumbnail->move('uploads', $fileName);

  $recipeData = $validator->validated();

  Recipe::where('idresep', $id)->update([
   'judul' => $recipeData['judul'],
   'gambar' => 'uploads/' . $fileName,
   'cara_pembuatan' => $recipeData['cara_pembuatan'],
   'video' => $recipeData['video'],
   'user_email' => $recipeData['user_email'],
   'status_resep' => 'submit',
  ]);

  Ingredient::where('resep_idresep', $id)->delete();

  Tool::where('resep_idresep', $id)->delete();

  foreach (json_decode($request->bahan) as $bahan) {
   Ingredient::create([
    'nama' => $bahan->nama,
    'satuan' => $bahan->satuan,
    'banyak' => $bahan->banyak,
    'keterangan' => $bahan->keterangan,
    'resep_idresep' => $id,
   ]);
  }

  foreach (json_decode($request->alat) as $alat) {
   Tool::create([
    'nama_alat' => $alat->nama,
    'keterangan' => $alat->keterangan,
    'resep_idresep' => $id,
   ]);
  }

  return response()->json([
   'data' => [
    'msg' => 'Resep Berhasil Di Edit',
    'resep' => $recipeData['judul'],
   ]
  ], 200);
 }

 public function delete_recipe($id)
 {
  Tool::where('resep_idresep', $id)->delete();
  Recipe::where('idresep', $id)->delete();
  Ingredient::where('resep_idresep', $id)->delete();

  return response()->json([
   'data' => [
    'msg' => 'Resep Berhasil Di Hapus',
    'resep_id' => $id
   ]
  ], 200);
 }

 public function publish_recipe($id)
 {
  $recipe = Recipe::where('idresep', $id)->get();

  if ($recipe) {
   Recipe::where('idresep', $id)->update(['status_resep' => 'publish']);

   Log::create([
    'module' => 'Publish Resep',
    'action' => 'Publish Resep Dengan ID : ' . $id,
    'useraccess' => 'administrator',
   ]);

   return response()->json([
    'data' => [
     'msg' => 'Resep Dengan ID : ' . $id . ', Berhasil Di Publish'
    ]
   ], 200);
  }

  return response()->json([
   'data' => [
    'msg' => 'Resep Dengan ID : ' . $id . ', Tidak Di Temukan'
   ]
  ], 422);
 }

 public function unpublish_recipe($id)
 {
  $recipe = Recipe::where('idresep', $id)->get();

  if ($recipe) {
   Recipe::where('idresep', $id)->update(['status_resep' => 'unpublished']);

   Log::create([
    'module' => 'Publish Resep',
    'action' => 'Unublish Resep Dengan ID : ' . $id,
    'useraccess' => 'administrator',
   ]);

   return response()->json([
    'data' => [
     'msg' => 'Resep Dengan ID : ' . $id . ', Berhasil Di Unpublish'
    ]
   ], 200);
  }

  return response()->json([
   'data' => [
    'msg' => 'Resep Dengan ID : ' . $id . ', Tidak Di Temukan'
   ]
  ], 422);
 }

 public function dashboard()
 {
  $totalRecipes = Recipe::count();

  $totalPublishRecipes = Recipe::where('status_resep', 'publish')->count();

  $popularRecipe = DB::table('resep')->select('judul', DB::raw('count(idresep_view) as jumlah'))->leftJoin('resep_view', 'resep.idresep', '=', 'resep_view.resep_idresep')->groupBy('judul')->orderBy(DB::raw('count(idresep_view)'), 'desc')->limit(10)->get();

  return response()->json([
   'data' => [
    'msg' => 'Dashboard Monitoring',
    'Total Recipes' => $totalRecipes,
    'Total Publish Recipes' => $totalPublishRecipes,
    'Popular Recipe' => $popularRecipe
   ]
  ], 200);
 }

 public function showrecipebyid($id){
    $recipe = Recipe::where('idresep', $id)->first();
    if (!$recipe) {
        return response()->json([
            "msg" => "Recipe List",
            "data" => "Not Found"
        ], 404);
    }

    return response()->json([
        "msg" => "Recipe List",
        "data" => $recipe
    ], 200);

}

public function showrecipes(){
    {
        $recipes = Recipe::all();

        $data = [];
        foreach ($recipes as $recipe) {
            $recipe['gambar'] = url('uploads/' . $recipe['gambar']);
            $data[] = $recipe;
        }
        return response()->json([
            "msg" => "Recipe List",
            "data" => $recipes
        ], 200);

    }
}
}
