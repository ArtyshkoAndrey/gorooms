<?php

namespace App\Http\Controllers\Admin;

use App\Models\Instruction;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Foundation\Application;

class InstructionController extends Controller
{

  /**
   * @return Application|Factory|View
   */
  public function index()
  {
    $instructions = Instruction::all();

    return view('admin.instructions.index', compact('instructions'));
  }

  /**
   *
   * @return Application|Factory|View
   */
  public function create()
  {
    return view('admin.instructions.create');
  }

  /**
   *
   * @return Application|Factory|View
   */
  public function edit(Instruction $instruction)
  {

    return view('admin.instructions.edit', compact('instruction'));
  }

  /**
   * @param Request $request
   *
   * @return RedirectResponse
   */
  public function store(Request $request): RedirectResponse
  {
    $i = new Instruction($request->only(['header', 'content']));
    $i->save();
    return redirect()->route('admin.instructions.index');
  }

  /**
   * @param Instruction $instruction
   * @param Request     $request
   *
   * @return RedirectResponse
   */
  public function update(Instruction $instruction, Request $request): RedirectResponse
  {
    $instruction->update($request->only(['header', 'content']));
    $instruction->save();
    return redirect()->route('admin.instructions.index');
  }

  public function destroy (Instruction $instruction): RedirectResponse
  {
    $instruction->delete();
    return redirect()->route('admin.instructions.index');
  }

}