<?php

namespace App\Controllers;

use App\Models\RegimeModel;

class RegimeController extends BaseController
{
    public function index()
    {
        $model = new RegimeModel();
        $data['regimes'] = $model->orderBy('id', 'DESC')->findAll();

        return view('admin/regimes_list', $data);
    }

    public function create()
    {
        return view('admin/regime_form', ['mode' => 'create', 'regime' => []]);
    }

    public function store()
    {
        $rules = [
            'nom' => 'required|min_length[3]',
            'variation_poids' => 'required|numeric',
            'duree_jours' => 'required|integer',
            'prix' => 'required|numeric',
            'pourcentage_viande' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'pourcentage_poisson' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'pourcentage_volaille' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        ];

        if (! $this->validate($rules)) {
            return view('admin/regime_form', ['mode' => 'create', 'regime' => $this->request->getPost(), 'errors' => $this->validator->getErrors()]);
        }

        $model = new RegimeModel();
        $model->insert([
            'nom' => $this->request->getPost('nom'),
            'variation_poids' => $this->request->getPost('variation_poids'),
            'duree_jours' => $this->request->getPost('duree_jours'),
            'prix' => $this->request->getPost('prix'),
            'pourcentage_viande' => $this->request->getPost('pourcentage_viande'),
            'pourcentage_poisson' => $this->request->getPost('pourcentage_poisson'),
            'pourcentage_volaille' => $this->request->getPost('pourcentage_volaille'),
        ]);

        return redirect()->to('/admin')->with('success', 'Régime créé.');
    }

    public function edit($id)
    {
        $model = new RegimeModel();
        $regime = $model->find((int) $id);

        if (! $regime) {
            return redirect()->to('/admin/regimes')->with('erreur', 'Régime introuvable.');
        }

        return view('admin/regime_form', ['mode' => 'edit', 'regime' => $regime]);
    }

    public function update($id)
    {
        $rules = [
            'nom' => 'required|min_length[3]',
            'variation_poids' => 'required|numeric',
            'duree_jours' => 'required|integer',
            'prix' => 'required|numeric',
            'pourcentage_viande' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'pourcentage_poisson' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'pourcentage_volaille' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        ];

        if (! $this->validate($rules)) {
            return view('admin/regime_form', ['mode' => 'edit', 'regime' => array_merge($this->request->getPost(), ['id' => $id]), 'errors' => $this->validator->getErrors()]);
        }

        $model = new RegimeModel();
        $model->update((int) $id, [
            'nom' => $this->request->getPost('nom'),
            'variation_poids' => $this->request->getPost('variation_poids'),
            'duree_jours' => $this->request->getPost('duree_jours'),
            'prix' => $this->request->getPost('prix'),
            'pourcentage_viande' => $this->request->getPost('pourcentage_viande'),
            'pourcentage_poisson' => $this->request->getPost('pourcentage_poisson'),
            'pourcentage_volaille' => $this->request->getPost('pourcentage_volaille'),
        ]);

        return redirect()->to('/admin')->with('success', 'Régime mis à jour.');
    }

    public function delete($id)
    {
        $model = new RegimeModel();
        $model->delete((int) $id);

        return redirect()->to('/admin')->with('success', 'Régime supprimé.');
    }
}
