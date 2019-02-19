<?php

namespace App\Controllers;

use App\Models\Contact;
use Core\View;

class Contacts
{
    public function index()
    {
        $data = filter_input_array(INPUT_GET, FILTER_SANITIZE_SPECIAL_CHARS);
        $start_point = 0;
        $per_page = 8;
        $keyword = null;

        if (isset($data['page'])) {
            $start_point = ($data['page'] - 1) * $per_page;
        }

        $contact = new Contact();
        $contacts = $contact->getContacts($start_point, $per_page);
        $pages = ceil($contact->numberOfContacts()/$per_page);
        $total = $contact->numberOfContacts();

        if(isset($data['search'])) {
            $keyword = $data['search'];
            $contacts = $contact->searchData($start_point, $per_page, $keyword);
            $pages = ceil($contact->searchResults($keyword)/$per_page);
        }
        if(isset($data['order'])){
            $contacts = $contact->getContacts($start_point, $per_page);
        }
        View::renderTemplate('contacts/index.html', [
            'contacts' => $contacts,
            'pages' => $pages,
            'keyword' => $keyword,
            'total' => $total
            ]);
    }

    public function addNew()
    {
        $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        $contact = new Contact($data); 

        if ($contact->store()) {
            header('Location: http://' . $_SERVER['HTTP_HOST'] . '/', true, 303);
            exit;
        } else {
            View::renderTemplate('contacts/index.html', [
                'contacts' => $contact->getContacts(0, 8),
                'errors' => $contact->getErrors(),
                'pages' => ceil($contact->numberOfContacts()/8),
                'total' => $contact->numberOfContacts()
            ]);
        }
    }

    public function show()
    {
        $id = htmlspecialchars($_GET['id']);
        $contact = new Contact();

        View::renderTemplate('edit-contacts/index.html', [
            "contacts" => $contact->getOneContact($id)
        ]);
    }

    public function edit()
    {
        $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        $id = $data['contact_id'];
        $contact = new Contact($data);

        if ($contact->update($id)) {
            header('Location: http://' . $_SERVER['HTTP_HOST'] . '/', true, 303);
            exit;
        } else {
            View::renderTemplate('edit-contacts/index.html', [
                'contacts' => $contact->getOneContact($id),
                'errors' => $contact->getErrors(),
                'total' => $contact->numberOfContacts()
            ]);
        }
    }

    public function delete()
    {
        $id = htmlspecialchars($_POST['contact_id']);
        $contact = new Contact();
        $contact->destroy($id);
        header('Location: http://' . $_SERVER['HTTP_HOST'] . '/', true, 303);
        exit;
    }
}