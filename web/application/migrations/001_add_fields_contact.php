<?php
/**
 * Created by PhpStorm.
 * User: hyunseok
 * Date: 14. 1. 19
 * Time: 오전 11:41
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_fields_contact extends CI_Migration {

    public function up()
    {
        $fields = array(
            'content' => array(
                'type' => 'text',
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            )
        );

        $this->dbforge->add_column('contact', $fields);
    }

    public function down()
    {
        if ($this->db->field_exists('content', 'contact'))
        {
            $this->dbforge->drop_column('contact', 'content');
        }

        if ($this->db->field_exists('ip_address', 'contact'))
        {
            $this->dbforge->drop_column('contact', 'ip_address');
        }
    }
}