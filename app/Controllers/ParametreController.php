<?php

namespace App\Controllers;

use App\Models\ParametreModel;

class ParametreController extends BaseController
{
    public function index()
    {
        $model = new ParametreModel();
        $data['parameters'] = $model->orderBy('id', 'ASC')->findAll();

        return view('admin/parametres_list', $data);
    }

    public function create()
    {
        return view('admin/parametre_form', ['mode' => 'create', 'parametre' => []]);
    }

    public function store()
    {
        $rules = [
            'cle' => 'required|min_length[2]',
            'libelle' => 'required|min_length[2]',
            'valeur' => 'required',
        ];

        if (! $this->validate($rules)) {
            return view('admin/parametre_form', ['mode' => 'create', 'parametre' => $this->request->getPost(), 'errors' => $this->validator->getErrors()]);
        }

        $model = new ParametreModel();
        $model->insert([
            'cle' => $this->request->getPost('cle'),
            'libelle' => $this->request->getPost('libelle'),
            'valeur' => $this->request->getPost('valeur'),
            'type' => $this->request->getPost('type') ?? 'text',
            'description' => $this->request->getPost('description') ?? null,
        ]);

        return redirect()->to('/admin/parametres')->with('success', 'Paramètre créé.');
    }

    public function edit($id)
    {
        $model = new ParametreModel();
        $param = $model->find((int) $id);

        if (! $param) {
            return redirect()->to('/admin/parametres')->with('erreur', 'Paramètre introuvable.');
        }

        return view('admin/parametre_form', ['mode' => 'edit', 'parametre' => $param]);
    }

    public function update($id)
    {
        $rules = [
            'cle' => 'required|min_length[2]',
            'libelle' => 'required|min_length[2]',
            'valeur' => 'required',
        ];

        if (! $this->validate($rules)) {
            return view('admin/parametre_form', ['mode' => 'edit', 'parametre' => array_merge($this->request->getPost(), ['id' => $id]), 'errors' => $this->validator->getErrors()]);
        }

        $model = new ParametreModel();
        $model->update((int) $id, [
            'cle' => $this->request->getPost('cle'),
            'libelle' => $this->request->getPost('libelle'),
            'valeur' => $this->request->getPost('valeur'),
            'type' => $this->request->getPost('type') ?? 'text',
            'description' => $this->request->getPost('description') ?? null,
        ]);

        return redirect()->to('/admin/parametres')->with('success', 'Paramètre mis à jour.');
    }

    public function delete($id)
    {
        $model = new ParametreModel();
        $model->delete((int) $id);

        return redirect()->to('/admin/parametres')->with('success', 'Paramètre supprimé.');
    }
}
