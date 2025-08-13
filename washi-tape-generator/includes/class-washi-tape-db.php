<?php

/**
 * Washi Tape Database Class
 */
class Washi_Tape_DB
{

    /**
     * Table name
     */
    private $table_name;

    /**
     * Constructor
     */
    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'washi_tapes';
    }

    /**
     * Create database tables
     */
    public function create_tables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            svg longtext NOT NULL,
            settings longtext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        // Add error logging
        if ($wpdb->last_error) {
            error_log('Washi Tape Table Creation Error (DB Class): ' . $wpdb->last_error);
        } else {
            error_log('Washi Tape Table Creation Success (DB Class)');
        }
    }

    /**
     * Create a new washi tape design
     */
    public function create_washi_tape($title, $svg, $settings)
    {
        global $wpdb;

        $current_time = current_time('mysql');

        $result = $wpdb->insert(
            $this->table_name,
            array(
                'title' => $title,
                'svg' => $svg,
                'settings' => $settings,
                'updated_at' => $current_time
            ),
            array('%s', '%s', '%s', '%s')
        );

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Update an existing washi tape design
     */
    public function update_washi_tape($id, $title, $svg, $settings)
    {
        global $wpdb;

        $current_time = current_time('mysql');

        $result = $wpdb->update(
            $this->table_name,
            array(
                'title' => $title,
                'svg' => $svg,
                'settings' => $settings,
                'updated_at' => $current_time
            ),
            array('id' => $id),
            array('%s', '%s', '%s', '%s'),
            array('%d')
        );

        return $result !== false ? $id : false;
    }

    /**
     * Delete a washi tape design
     */
    public function delete_washi_tape($id)
    {
        global $wpdb;

        $result = $wpdb->delete(
            $this->table_name,
            array('id' => $id),
            array('%d')
        );

        return $result !== false;
    }

    /**
     * Get a single washi tape design
     */
    public function get_washi_tape($id)
    {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT * FROM $this->table_name WHERE id = %d",
            $id
        );

        return $wpdb->get_row($query);
    }

    /**
     * Get all washi tape designs
     */
    public function get_all_washi_tapes()
    {
        global $wpdb;

        $query = "SELECT * FROM $this->table_name ORDER BY updated_at DESC";

        return $wpdb->get_results($query);
    }
}
