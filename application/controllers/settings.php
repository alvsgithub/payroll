<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Settings extends CI_Controller {

    private $limit = 10;

    public function __construct() {
        parent::__construct();
        $this->load->model('Setting');
        $this->sess_username = $this->session->userdata('username');
        $this->sess_role_id = $this->session->userdata('sess_role_id');
        $this->sess_staff_id = $this->session->userdata('sess_staff_id');
        $this->session->userdata('logged_in') == true ? '' : redirect('users/sign_in');
    }

    public function index($offset = 0) {
//        $this->filter_access('settings', 'roled_select', base_url());
        redirect('settings/edit/1');

        $settings = new Setting();

        switch ($this->input->get('c')) {
            case "1":
                $data['col'] = "name";
                break;
            case "2":
                $data['col'] = "value";
                break;
            case "3":
                $data['col'] = "id";
                break;
            default:
                $data['col'] = "id";
        }

        if ($this->input->get('d') == "1") {
            $data['dir'] = "DESC";
        } else {
            $data['dir'] = "ASC";
        }

        $data['title'] = "Settings Parameter";
        $data['btn_add'] = anchor('settings/add', 'Add New', "class='btn btn-primary'");
        $data['btn_home'] = anchor(base_url(), 'Home', "class='btn btn-home'");

        $uri_segment = 3;
        $offset = $this->uri->segment($uri_segment);

        if ($this->input->get('search_by')) {
            $total_rows = $settings->like($_GET['search_by'], $_GET['q'])->count();
            $settings->like($_GET['search_by'], $_GET['q'])->order_by($data['col'], $data['dir']);
        } else {
            $total_rows = $settings->count();
            $settings->order_by($data['col'], $data['dir']);
        }

        $data['settings'] = $settings->get($this->limit, $offset)->all;

        $config['base_url'] = site_url("settings/index");
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $this->limit;
        $config['uri_segment'] = $uri_segment;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('settings/index', $data);
    }

    function add() {
//        $this->filter_access('settings', 'roled_add', 'settings/index');
        redirect('settings/edit/1');
        $data['title'] = 'Add New';
        $data['form_action'] = site_url('settings/save');
        $data['link_back'] = anchor('settings/', 'Back', array('class' => 'btn btn-danger'));

        $data['id'] = '';
        $data['name'] = array('name' => 'name');
        $data['value'] = array('name' => 'value');
        $data['btn_save'] = array('name' => 'btn_save', 'value' => 'Save', "class" => "btn btn-primary");

        $this->load->view('settings/frm_settings', $data);
    }

    function edit($id) {
//        $this->filter_access('Config', 'roled_edit', 'settings/index');

        $setting = new Setting();
        $rs = $setting->where('id', $id)->get();
        $data['id'] = $rs->id;
        $data['logo'] = $rs->logo;
        $data['company_name'] = array('name' => 'company_name', 'value' => $rs->company_name);
        $data['address'] = array('name' => 'address', 'value' => $rs->address);
        $data['phone'] = array('name' => 'phone', 'value' => $rs->phone);
        $data['fax'] = array('name' => 'fax', 'value' => $rs->fax);
        $data['email'] = array('name' => 'email', 'value' => $rs->email);
        $data['city'] = array('name' => 'city', 'value' => $rs->city);
        $data['no_npwp'] = array('name' => 'no_npwp', 'value' => $rs->no_npwp);

        $data['btn_save'] = array('name' => 'btn_save', 'value' => 'Update', "class" => "btn btn-primary");

        $data['title'] = 'Update Setting';
        $data['form_action'] = site_url('settings/update');
        $data['link_back'] = anchor('settings/', 'Back', array("class" => "btn btn-danger"));

        $this->load->view('settings/frm_settings', $data);
    }

    function save() {
//        $this->filter_access('Config', 'roled_add', 'settings/index');
        redirect('settings/edit/1');
        $setting = new Setting();
        $setting->logo = $this->input->post('logo');
        $setting->company_name = $this->input->post('company_name');
        $setting->address = $this->input->post('address');
        $setting->phone = $this->input->post('phone');
        $setting->fax = $this->input->post('fax');
        $setting->email = $this->input->post('email');
        $setting->city = $this->input->post('city');
        $setting->no_npwp = $this->input->post('no_npwp');

        if ($setting->save()) {
            $this->session->set_flashdata('message', 'Setting successfully created!');
            redirect('settings/');
        } else {
            // Failed
            $setting->error_message('custom', 'Config Name required');
            $msg = $setting->error->custom;
            $this->session->set_flashdata('message', $msg);
            redirect('settings/add');
        }
    }

    function update() {
        $setting = new Setting();

        // upload photo
        $config['upload_path'] = 'assets/upload';
        $config['allowed_types'] = 'gif|jpg|png|bmp';
        $this->load->library("upload", $config);
        if ($this->upload->do_upload("logo")) {
            $data = $this->upload->data();
            //print_r($data["file_name"]);
            //$setting->logo = $data["file_name"];
        } else {
            //print_r($this->upload->display_errors());
        }
        
        $setting->where('id', $this->input->post('id'))
                ->update(array(
                    'logo' => $data["file_name"],
                    'company_name' => $this->input->post('company_name'),
                    'address' => $this->input->post('address'),
                    'phone' => $this->input->post('phone'),
                    'fax' => $this->input->post('fax'),
                    'email' => $this->input->post('email'),
                    'city' => $this->input->post('city'),
                    'no_npwp' => $this->input->post('no_npwp')
                        )
        );

        $this->session->set_flashdata('message', 'Config Update successfuly.');
        redirect('settings/edit/1');
    }

    function delete($id) {
        redirect('settings/edit/1');
        $this->filter_access('Config', 'roled_delete', 'settings/index');

        $setting = new Setting();
        $setting->_delete($id);

        $this->session->set_flashdata('message', 'Config successfully deleted!');
        redirect('settings/');
    }

    function to_excel() {
        $this->load->view('settings/to_excel');
    }

    function filter_access($module, $field, $page) {
        $user = new User();
        $status_access = $user->get_access($this->sess_role_id, $module, $field);

        if ($status_access == false) {
            $msg = '<div class="alert alert-error">You do not have access to this page, please contact administrator</div>';
            $this->session->set_flashdata('message', $msg);
            redirect($page);
        }
    }

}

