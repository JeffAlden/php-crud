<?php
class Database {
    private $dbname;
    private $tblname;
    private $host;
    private $username;
    private $password;
    public $conn;

    function __construct() {
        // Database Name
        $this->dbname = 'php_crud_db';
        // Table Name
        $this->tblname = 'record_list';
        // Host Name
        $this->host = 'localhost';
        // DB Username
        $this->username = 'root';
        // Database Name
        $this->password = '';

        $this->conn = new mysqli($this->host, $this->username, $this->password);

        // Creating the sample DB
        $db_sql = "CREATE DATABASE IF NOT EXISTS {$this->dbname}";
        $this->conn->query($db_sql);
        if (!$this->conn->error) {
            if ($this->conn->affected_rows > 0) {
                $this->conn->close();
                // Open Database Connection
                $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);

                // Creating a sample table
                $tbl_sql = "CREATE TABLE IF NOT EXISTS `{$this->tblname}`
                ( `id` int(30) NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `name` varchar(250) NOT NULL,
                `email` varchar(250) NOT NULL,
                `description` text NULL,
                `username` text NOT NULL,
                `password` text NOT NULL,
                `site` text NOT NULL,
                `date_added` DATETIME NOT NULL Default CURRENT_TIMESTAMP,
                `date_updated` DATETIME NULL Default NULL ON UPDATE CURRENT_TIMESTAMP)";
                $this->conn->query($tbl_sql);
                if ($this->conn->error) {
                    die("Creating DB Table Failed. Error: " . $this->conn->error);
                }
            } else {
                $this->conn->close();
                // Open Database Connection
                $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
            }
        } else {
            die("Creating DB Failed. Error: " . $this->conn->error);
        }
    }

    // The refValues function for use in get_results
    private function refValues($arr) {
        $refs = [];
        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }

    // Fetching Data From Database with parameter binding using prepared statements
    public function get_results($query = "", $params = []) {
        if (empty($query)) {
            // Returning an error if the query string is empty
            return json_encode(["error" => "Query is empty"]);
        } else {
            // Prepare the query
            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                return json_encode(["error" => $this->conn->error]);
            }

            // Bind parameters
            if (!empty($params)) {
                $types = "";
                $values = [];
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= "i"; // Integer
                    } elseif (is_double($param)) {
                        $types .= "d"; // Double
                    } else {
                        $types .= "s"; // String
                    }
                    $values[] = $param;
                }
                $bindParams = array_merge([$types], $values);
                call_user_func_array([$stmt, "bind_param"], $this->refValues($bindParams));
            }

            // Execute the prepared statement
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result === false) {
                    return json_encode(["error" => $this->conn->error]);
                }

                // Fetch the results
                $data = [];
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }

                $stmt->close();

                return $data;
            } else {
                return json_encode(["error" => $this->conn->error]);
            }
        }
    }

    public function sanitize_data($string = "") {
        if (!is_numeric($string)) {
            $string = addslashes($this->conn->real_escape_string($string));
        }
        return $string;
    }

    public function insert($data = []) {
        if (!is_array($data)) {
            die("The given data is not an Array. Data Given" . $data);
        } elseif (is_array($data) && count($data) <= 0) {
            die("The given data is null.");
        } else {
            $idata = "";
            $allowed_field = ["name", "email", "username", "description", "password", "site", "date_updated", "date_added"];
            foreach ($data as $k => $v) {
                if (!in_array($k, $allowed_field))
                    continue;
                $v = $this->sanitize_data($v);
                if (!empty($idata)) $idata .= ", ";
                $idata .= "`{$k}` = '{$v}'";
            }
            $insert = $this->conn->query("INSERT INTO `{$this->tblname}` set {$idata}");
            if ($insert) {
                return $this->conn->insert_id;
            } else {
                return false;
            }
        }
    }

    public function update($data = []) {
        if (!is_array($data)) {
            die("The given data is not an Array. Data Given" . $data);
        } elseif (is_array($data) && count($data) <= 0) {
            die("The given data is null.");
        } else {
            $idata = "";
            $allowed_field = ["name", "email", "username", "description", "password", "site", "date_updated", "date_added"];
            foreach ($data as $k => $v) {
                if (!in_array($k, $allowed_field))
                    continue;
                $v = $this->sanitize_data($v);
                if (!empty($idata)) $idata .= ", ";
                $idata .= "`{$k}` = '{$v}'";
            }
            $id = $this->sanitize_data($data['id']);
            $update = $this->conn->query("UPDATE `{$this->tblname}` set {$idata} where id = '{$id}'");
            if ($update) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function single_fetch($id = "") {
        if (empty($id)) {
            die("The given id is empty.");
        } else {
            $id = $this->sanitize_data($id);
            $get = $this->conn->query("SELECT * FROM `{$this->tblname}` where id = '{$id}'");
            if (!$this->conn->error) {
                if ($get->num_rows > 0)
                    return $get->fetch_assoc();
                else
                    return [];
            } else {
                die("An error occurred. Error: " . $this->conn->error);
            }
        }
    }

    public function delete($id = "") {
        if (empty($id)) {
            die("The given id is empty.");
        } else {
            $id = $this->sanitize_data($id);
            $delete = $this->conn->query("DELETE FROM `{$this->tblname}` where id = '{$id}'");
            if ($this->conn->error) {
                die("An error occurred. Error: " . $this->conn->error);
            }
            if ($delete) {
                return true;
            } else {
                return false;
            }
        }
    }

    // Closing db connection
    function __destruct() {
        $this->conn->close();
    }
}
?>
