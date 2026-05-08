<?php

namespace App\Controllers;

use App\Models\ActiviteModel;

class ActiviteController extends BaseController
{
    public function index()
    {
        $model = new ActiviteModel();
        $data['activites'] = $model->orderBy('id', 'DESC')->findAll();

        return view('admin/activites_list', $data);
    }

    public function create()
    {
        return view('admin/activite_form', ['mode' => 'create', 'activite' => []]);
    }

    public function store()
    {
        $rules = [
            'nom' => 'required|min_length[3]',
            'calories_par_heure' => 'required|integer',
            'duree_recommandee_min' => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            return view('admin/activite_form', ['mode' => 'create', 'activite' => $this->request->getPost(), 'errors' => $this->validator->getErrors()]);
        }

        $model = new ActiviteModel();
        $model->insert([
            'nom' => $this->request->getPost('nom'),
            'calories_par_heure' => $this->request->getPost('calories_par_heure'),
            'duree_recommandee_min' => $this->request->getPost('duree_recommandee_min'),
        ]);

        return redirect()->to('/admin/activites')->with('success', 'Activité créée.');
    }

    public function edit($id)
    {
        $model = new ActiviteModel();
        $activite = $model->find((int) $id);

        if (! $activite) {
            return redirect()->to('/admin/activites')->with('erreur', 'Activité introuvable.');
        }

        return view('admin/activite_form', ['mode' => 'edit', 'activite' => $activite]);
    }

    public function update($id)
    {
        $rules = [
            'nom' => 'required|min_length[3]',
            'calories_par_heure' => 'required|integer',
            'duree_recommandee_min' => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            return view('admin/activite_form', ['mode' => 'edit', 'activite' => array_merge($this->request->getPost(), ['id' => $id]), 'errors' => $this->validator->getErrors()]);
        }

        $model = new ActiviteModel();
        $model->update((int) $id, [
            'nom' => $this->request->getPost('nom'),
            'calories_par_heure' => $this->request->getPost('calories_par_heure'),
            'duree_recommandee_min' => $this->request->getPost('duree_recommandee_min'),
        ]);

        return redirect()->to('/admin/activites')->with('success', 'Activité mise à jour.');
    }

    public function delete($id)
    {
        $model = new ActiviteModel();
        $model->delete((int) $id);

        return redirect()->to('/admin/activites')->with('success', 'Activité supprimée.');
    }
}
