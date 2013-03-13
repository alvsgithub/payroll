<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Branches extends CI_Controller {

    private $limit = 10;

    public function __construct() {
        parent::__construct();
        $this->load->model('Branch');
        $this->session->userdata('logged_in') == true ? '' : redirect('users/sign_in');
    }

    public function index($offset = 0) {
        $branch_list = new Branch();
        $total_rows = $branch_list->count();

        switch ($this->input->get('c')) {
            case "1":
                $data['col'] = "branch_name";
                break;
            case "2":
                $data['col'] = "branch_id";
                break;
            default:
                $data['col'] = "branch_id";
        }

        if ($this->input->get('d') == "1") {
            $data['dir'] = "DESC";
        } else {
            $data['dir'] = "ASC";
        }

        $data['title'] = "Branch";
        $data['btn_add'] = anchor('branches/add', 'Add New', "class='btn btn-primary'");
        $data['btn_home'] = anchor(base_url(), 'Home', "class='btn btn-home'");

        $uri_segment = 3;
        $offset = $this->uri->segment($uri_segment);

        $branch_list->order_by($data['col'], $data['dir']);

        $data['branch_list'] = $branch_list->get($this->limit, $offset)->all;

        $config['base_url'] = site_url("branches/index");
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $this->limit;
        $config['uri_segment'] = $uri_segment;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('branches/index', $data);
    }

    function add() {
        $data['title'] = 'Add New Branch';
        $data['form_action'] = site_url('branches/save');
        $data['link_back'] = anchor('branches/', 'Back', array('class' => 'btn'));

        $data['id'] = '';
        $data['branch_name'] = array('name' => 'branch_name');
        $data['btn_save'] = array('name' => 'btn_save', 'value' => 'Save', "class" => "btn btn-primary");

        $this->load->view('branches/frm_branch', $data);
    }

    function edit($id) {
        $branch = new Branch();

        $rs = $branch->where('branch_id', $id)->get();
        $data['id'] = $rs->branch_id;
        $data['branch_name'] = array('name' => 'branch_name', 'value' => $rs->branch_name);
        $data['btn_save'] = array('name' => 'btn_save', 'value' => 'Update', "class" => "btn btn-primary");

        $data['title'] = 'Update Branch';
        $data['form_action'] = site_url('branches/update');
        $data['link_back'] = anchor('branches/', 'Back', array("class" => "btn"));

        $this->load->view('branches/frm_branch', $data);
    }

    function save() {
        $branch = new Branch();
        $branch->branch_name = $this->input->post('branch_name');
        if ($branch->save()) {
            $this->session->set_flashdata('message', 'Branch successfully created!');
            redirect('branches/');
        } else {
            // Failed
            $branch->error_message('custom', 'Branch Name required');
            $msg = $branch->error->custom;
            $this->session->set_flashdata('message', $msg);
            redirect('branches/add');
        }
    }

    function update() {
        $branch = new Branch();
        $branch->where('branch_id', $this->input->post('id'))
                ->update('branch_name', $this->input->post('branch_name'));

        $this->session->set_flashdata('message', 'Branch Update successfuly.');
        redirect('branches/');
    }

    function delete($id) {
        $branch = new Branch();
        $branch->_delete($id);

        $this->session->set_flashdata('message', 'Branch successfully deleted!');
        redirect('branches/');
    }

}
