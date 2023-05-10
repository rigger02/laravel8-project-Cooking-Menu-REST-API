<?php

namespace App\Observers;

use App\Models\Log;
use App\Models\Ingredient;

class IngredientObserver
{
 /**
  * Handle the Ingredient "created" event.
  *
  * @param  \App\Models\Ingredient  $ingredient
  * @return void
  */
 public function created(Ingredient $ingredient)
 {
  Log::create([
   'module' => 'Tambah Bahan',
   'action' => 'Tambah Bahan Untuk ID Resep ' . $ingredient->resep_idresep . ' Dengan Nama Bahan ' . $ingredient->nama,
   'useraccess' => '-',
  ]);
 }

 /**
  * Handle the Ingredient "updated" event.
  *
  * @param  \App\Models\Ingredient  $ingredient
  * @return void
  */
 public function updated(Ingredient $ingredient)
 {
  Log::create([
   'module' => 'Edit Bahan',
   'action' => 'Edit Bahan Untuk ID Resep ' . $ingredient->resep_idresep . ' Dengan Nama Bahan ' . $ingredient->nama,
   'useraccess' => '-',
  ]);
 }

 /**
  * Handle the Ingredient "deleted" event.
  *
  * @param  \App\Models\Ingredient  $ingredient
  * @return void
  */
 public function deleted(Ingredient $ingredient)
 {
  Log::create([
   'module' => 'Hapus Bahan',
   'action' => 'Hapus Bahan Untuk ID Resep ' . $ingredient->resep_idresep,
   'useraccess' => '-',
  ]);
 }

 /**
  * Handle the Ingredient "restored" event.
  *
  * @param  \App\Models\Ingredient  $ingredient
  * @return void
  */
 public function restored(Ingredient $ingredient)
 {
  //
 }

 /**
  * Handle the Ingredient "force deleted" event.
  *
  * @param  \App\Models\Ingredient  $ingredient
  * @return void
  */
 public function forceDeleted(Ingredient $ingredient)
 {
  //
 }
}
