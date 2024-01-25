<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use File;


class StudentController extends Controller
{
    public function create()
    {
        return view('student.create');
    }

    public function index()
    {
        $mahasiswa = Student::all();
        return view('student.index', ['students'=>$mahasiswa]);
    }

    public function show($student_id)
    {
        $result = Student::findOrFail($student_id);
        return view('student.show', ['student'=>$result]);
    }

    public function edit($student_id)
    {
        $result = Student::findOrFail($student_id);
        return view('student.edit', ['student'=>$result]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nim' => 'required|size:8,unique:students',
            'nama' => 'required|min:3|max:50',
            'jenis_kelamin' => 'required|in:P,L',
            'jurusan' => 'required',
            'alamat' => '',
            'image' => 'required|file|image|max:5000',
        ]);
        $mahasiswa = new Student();
        $mahasiswa->nim = $validatedData['nim'];
        $mahasiswa->name = $validatedData['nama'];
        $mahasiswa->gender = $validatedData['jenis_kelamin'];
        $mahasiswa->departement = $validatedData['jurusan'];
        $mahasiswa->address = $validatedData['alamat'];
        if ($request->hasFile('image')) {
            $extFile = $request->image->getClientOriginalExtension();
            $namaFile = 'user-'.time().".".$extFile;
            $path = $request->image->move('assets/images', $namaFile);
            $mahasiswa->image = $path;
        }
        $mahasiswa->save();
        $request->session()->flash('pesan', "Penambahan data berhasil");
        return redirect()->route('student.index');
    }

    public function update(Request $request, Student $student)
    {
        $validatedData = $request->validate([
            'nim' => 'required|size:8,unique:students',
            'nama' => 'required|min:3|max:50',
            'jenis_kelamin' => 'required|in:P,L',
            'jurusan' => 'required',
            'alamat' => '',
            'image' => 'file|image|max:5000',
        ]);
        $student->nim = $validatedData['nim'];
        $student->name = $validatedData['nama'];
        $student->gender = $validatedData['jenis_kelamin'];
        $student->departement = $validatedData['jurusan'];
        $student->address = $validatedData['alamat'];
        if ($request->hasFile('image')) {
            $extFile = $request->image->getClientOriginalExtension();
            $namaFile = 'user-'.time().".".$extFile;
            File::delete($student->image);
            $path = $request->image->move('assets/images', $namaFile);
            $student->image = $path;
        }
        $student->save();   
        $request->session()->flash('pesan', "Perubahan data berhasil");
        return redirect()->route('student.show', ['student'=>$student->id]);
    }

    public function destroy(Request $request, Student $student)
    {
        File::delete($student->image);
        $student->delete();
        $request->session()->flash('pesan', "Penghapusan data berhasil");
        return redirect()->route('student.index');
    }
}
