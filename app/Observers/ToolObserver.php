<?php

namespace App\Observers;

use App\Models\Log;
use App\Models\Tool;

class ToolObserver
{
 /**
  * Handle the Tool "created" event.
  *
  * @param  \App\Models\Tool  $tool
  * @return void
  */
 public function created(Tool $tool)
 {
  Log::create([
   'module' => 'Tambah Alat',
   'action' => 'Tambah Alat Untuk ID Resep ' . $tool->resep_idresep . ' Dengan Nama Alat ' . $tool->nama_alat,
   'useraccess' => '-',
  ]);
 }

 /**
  * Handle the Tool "updated" event.
  *
  * @param  \App\Models\Tool  $tool
  * @return void
  */
 public function updated(Tool $tool)
 {
  Log::create([
   'module' => 'Edit Alat',
   'action' => 'Edit Alat Untuk ID Resep ' . $tool->resep_idresep . ' Dengan Nama Alat ' . $tool->nama_alat,
   'useraccess' => '-',
  ]);
 }

 /**
  * Handle the Tool "deleted" event.
  *
  * @param  \App\Models\Tool  $tool
  * @return void
  */
 public function deleted(Tool $tool)
 {
  Log::create([
   'module' => 'Hapus Alat',
   'action' => 'Hapus Alat Untuk ID Resep ' . $tool->resep_idresep,
   'useraccess' => '-',
  ]);
 }

 /**
  * Handle the Tool "restored" event.
  *
  * @param  \App\Models\Tool  $tool
  * @return void
  */
 public function restored(Tool $tool)
 {
  //
 }

 /**
  * Handle the Tool "force deleted" event.
  *
  * @param  \App\Models\Tool  $tool
  * @return void
  */
 public function forceDeleted(Tool $tool)
 {
  //
 }
}
