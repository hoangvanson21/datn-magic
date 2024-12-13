<?php

namespace App\Http\Controllers;

use App\Models\Options;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    public function index()
    {
        $options = Options::paginate(10);
        return view('admin.Options.index', compact('options'));
    }

    public function create()
    {
        return view('admin.Options.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'ram' => 'required|string|max:225',
            'rom' => 'required|string|max:225',
        ]);

        $data = $request->all();

        options::create($data);

        return redirect()->route('admin.Options.index')->with('success', 'Tạo màu sắc thành công !');
    }

    public function edit($id)
    {
        $options = Options::findOrFail($id);
        $ramOptions = ['4GB', '6GB', '8GB', '12GB', '16GB'];
        $romOptions = ['32GB', '64GB', '128GB', '256GB', '512GB'];

        return view('admin.Options.edit', compact('options', 'ramOptions', 'romOptions'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'ram' => 'required|string|max:225',
            'rom' => 'required|string|max:225',
        ]);

        $options = Options::findOrFail($id);
        $options->update($request->all());

        return redirect()->route('admin.options.index')->with('success', 'Cập nhật màu sắc thành công!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $options = Options::findOrFail($id);
        $options->delete();

        return redirect()->route('admin.options.index')->with('success', 'Xóa màu sắc thành công');
    }
}
