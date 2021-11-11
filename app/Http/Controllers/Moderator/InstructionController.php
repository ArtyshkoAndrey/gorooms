<?php

namespace App\Http\Controllers\Moderator;

use App\Models\Hotel;
use App\Models\Instruction;
use App\Http\Controllers\Controller;

class InstructionController extends Controller
{

  public function index (int $id)
  {
    $hotel = Hotel::findOrFail($id);
    $instructions = Instruction::all();
    return view('moderator.instruction.index', compact('hotel', 'instructions'));
  }

}