<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

define('END_TRANSACTION', 0);

class dbAccess {

       var $db_connect_id;
       var $query_result;
       var $row = array();
       var $rowset = array();
       var $num_queries = 0;

       //
       // Constructor
       function dbAccess($host,$username,$password,$dbname,$persistency = true) {

               $this->persistency = $persistency;
               $this->user = $username;
               $this->password = $password;
               $this->server = $host;
               $this->dbname = $dbname;
               $database = $dbname;

               if($this->persistency) {
                       $this->db_connect_id = @mysql_pconnect($this->server, $this->user, $this->password);
               } else {
                       $this->db_connect_id = @mysql_connect($this->server, $this->user, $this->password);
               }
               if($this->db_connect_id) {
                       if($database != "")     {
                               $this->dbname = $database;
                               $dbselect = @mysql_select_db($this->dbname);
                               if(!$dbselect) {
                                       @mysql_close($this->db_connect_id);
                                       $this->db_connect_id = $dbselect;
                               }
                       }
                       return $this->db_connect_id;
               } else {
                       return false;
               }
       }

       //
       // Other base methods
       //
       function destroy() {
               if($this->db_connect_id) {
                       if($this->query_result) {
                               @mysql_free_result($this->query_result);
                       }
                       $result = @mysql_close($this->db_connect_id);
                       return $result;
               } else {
                       return false;
               }
       }

       //
       // Base query method
       //
       function query($query = "", $transaction = FALSE) {
               // Remove any pre-existing queries
               unset($this->query_result);
               if($query != "") {
                       $this->num_queries++;
                       $this->query_result = @mysql_query($query, $this->db_connect_id);
               }
               if($this->query_result) {
                       unset($this->row[$this->query_result]);
                       unset($this->rowset[$this->query_result]);
                       return $this->query_result;
               } else {
                       return ( $transaction == END_TRANSACTION ) ? true : false;
               }
       }

       // array.key ==> value --> 'placeholder'=>"value"
       function bpquery($query,$values = array())
       {
            $query = "PREPARE statement FROM '$query'";
            $prepare_query = @mysql_query($query, $this->db_connect_id);
            if ($prepare_query) {
              $vals = "";
              $place_keys = "";
              foreach ($values as $key => $value) {
                if (!$vals == '') {
                  $vals .= ", @$key = '$value'";
                  $place_keys .= ", @$key";
                }else{
                  $vals = "@$key = '$value'";
                  $place_keys = "@$key";
                }
              }
              if ($vals != "") {
                $query = "SET ".$vals;
                $prepare_query = @mysql_query($query, $this->db_connect_id);
                echo "$query";
                $setstmt = " USING $place_keys;";
                echo "$setstmt";
              }
                if ($prepare_query) {
                  $query = "EXECUTE statement ".$setstmt;
                  echo "$query";
                  $this->query_result = @mysql_query($query, $this->db_connect_id);
                  if ($prepare_query) {
                    print_r($this->fetchrow());
                    unset($this->row[$this->query_result]);
                    unset($this->rowset[$this->query_result]);
                    echo mysql_error();
                    return $this->query_result;
                  };
                }
            }

       }

       //
       // Other query methods
       //
       function numrows($query_id = 0) {
               if(!$query_id) {
                       $query_id = $this->query_result;
               }
               if($query_id) {
                       $result = @mysql_num_rows($query_id);
                       return $result;
               } else {
                       return false;
               }
       }
       function affectedrows() {
               if($this->db_connect_id) {
                       $result = @mysql_affected_rows($this->db_connect_id);
                       return $result;
               } else {
                       return false;
               }
       }
       function numfields($query_id = 0) {
               if(!$query_id) {
                       $query_id = $this->query_result;
               }
               if($query_id) {
                       $result = @mysql_num_fields($query_id);
                       return $result;
               } else {
                       return false;
               }
       }
       function fieldname($offset, $query_id = 0) {
               if(!$query_id) {
                       $query_id = $this->query_result;
               }
               if($query_id) {
                       $result = @mysql_field_name($query_id, $offset);
                       return $result;
               } else {
                       return false;
               }
       }
       function fieldtype($offset, $query_id = 0) {
               if(!$query_id) {
                       $query_id = $this->query_result;
               }
               if($query_id) {
                       $result = @mysql_field_type($query_id, $offset);
                       return $result;
               } else {
                       return false;
               }
       }
       function fetchrow($query_id = 0) {
               if(!$query_id) {
                       $query_id = $this->query_result;
               }
               if($query_id) {
                       $this->row[(int)$query_id] = @mysql_fetch_array($query_id);
                       return $this->row[(int)$query_id];
               } else {
                       return false;
               }
       }
       function fetchrowset($query_id = 0) {
               if(!$query_id) {
                       $query_id = $this->query_result;
               }
               if($query_id) {
                   $result = array();
                       unset($this->rowset[$query_id]);
                       unset($this->row[$query_id]);
                       while($this->rowset[$query_id] = @mysql_fetch_array($query_id)) {
                               $result[] = $this->rowset[$query_id];
                       }
                       return $result;
               } else {
                       return false;
               }
       }
       function fetchfield($field, $rownum = -1, $query_id = 0) {
               if(!$query_id) {
                       $query_id = $this->query_result;
               }
               if($query_id) {
                       if($rownum > -1) {
                               $result = @mysql_result($query_id, $rownum, $field);
                       } else {
                               if(empty($this->row[$query_id]) && empty($this->rowset[$query_id])) {
                                       if($this->fetchrow()) {
                                               $result = $this->row[$query_id][$field];
                                       }
                               } else {
                                       if($this->rowset[$query_id]) {
                                               $result = $this->rowset[$query_id][$field];
                                       } else if($this->row[$query_id]) {
                                               $result = $this->row[$query_id][$field];
                                       }
                               }
                       }
                       return $result;
               } else {
                       return false;
               }
       }
       function rowseek($rownum, $query_id = 0){
               if(!$query_id) {
                       $query_id = $this->query_result;
               }
               if($query_id) {
                       $result = @mysql_data_seek($query_id, $rownum);
                       return $result;
               } else {
                       return false;
               }
       }
       function nextid(){
               if($this->db_connect_id) {
                       $result = @mysql_insert_id($this->db_connect_id);
                       return $result;
               } else {
                       return false;
               }
       }
       function freeresult($query_id = 0){
               if(!$query_id) {
                       $query_id = $this->query_result;
               }

               if ( $query_id ) {
                       unset($this->row[$query_id]);
                       unset($this->rowset[$query_id]);

                       @mysql_free_result($query_id);

                       return true;
               } else {
                       return false;
               }
       }
       function error($query_id = 0) {
               $result["message"] = @mysql_error($this->db_connect_id);
               $result["code"] = @mysql_errno($this->db_connect_id);

               return $result;
       }
}



