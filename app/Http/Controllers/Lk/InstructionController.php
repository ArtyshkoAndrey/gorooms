<?php

namespace App\Http\Controllers\Lk;

use App\Models\Instruction;
use App\Http\Controllers\Controller;

class InstructionController extends Controller
{

  public function index ()
  {
    $instructions = Instruction::all();
    return view('lk.instruction.index', compact('instructions'));
  }

}