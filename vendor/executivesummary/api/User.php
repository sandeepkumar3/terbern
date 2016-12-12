<?php

require_once 'vendors/executivesummary/configs/config.php';

class Users extends config
{

    /**
     * 
     * @param number $user_id
     */
    function getUserFlags($user_id = 0)
    {
        if ($user_id) {
             $strSQL = "SELECT `User`.`baseproduct_status` AS user_baseproduct_status, 
            		`User`.`fdny_status` AS user_fdny_status, `User`.`permit_status` AS user_permit_status,
            		 `User`.`hpd_status` AS user_hpd_status, `User`.`custom_item_flag` AS user_custom_item_flag,
            		 `User`.`work_order_flag` AS user_work_order_flag, `User`.`bx_flag` AS user_bx_flag, 
            		`User`.`dec_flag` AS user_dec_flag, `User`.`elevator_application_flag` AS user_elevator_application_flag,
            		 `User`.`ara_laa_flag` AS user_ara_laa_flag, `User`.`after_hour_variance_permit_flag` AS user_after_hour_variance_permit_flag,
            		 `User`.`dof_flag` AS user_dof_flag, `User`.`electrical_application_flag` AS user_electrical_application_flag,
            		 `User`.`cof_flag` AS user_cof_flag, `User`.`liens_sidewalk_flag` AS user_liens_sidewalk_flag, 
            		`User`.`fdny_permit_flag` AS user_fdny_permit_flag, `User`.`executive_email_alert_flag` AS user_executive_email_alert_flag,
            		User.representation_integration_flag as user_representation_integration_flag, User.es_hpd_status as user_es_hpd_status,
            		`Masteruser`.`baseproduct_status` AS master_baseproduct_status, 
            		`Masteruser`.`fdny_status` AS master_fdny_status, `Masteruser`.`permit_status` AS master_permit_status, 
            		`Masteruser`.`hpd_status` AS master_hpd_status, `Masteruser`.`custom_item_flag` AS master_custom_item_flag,
            		 `Masteruser`.`work_order_flag` AS master_work_order_flag, `Masteruser`.`bx_flag` AS master_bx_flag, `Masteruser`.`dec_flag` AS master_dec_flag,
            		 `Masteruser`.`elevator_application_flag` AS master_elevator_application_flag, `Masteruser`.`ara_laa_flag` AS master_ara_laa_flag,
            		 `Masteruser`.`after_hour_variance_permit_flag` AS master_after_hour_variance_permit_flag, `Masteruser`.`dof_flag` AS master_dof_flag, 
            		`Masteruser`.`electrical_application_flag` AS master_electrical_application_flag, `Masteruser`.`cof_flag` AS master_cof_flag,
            		 `Masteruser`.`liens_sidewalk_flag` AS master_liens_sidewalk_flag, `Masteruser`.`fdny_permit_flag` AS master_fdny_permit_flag, 
            		`Masteruser`.`executive_email_alert_flag` AS master_executive_email_alert_flag,
            		Masteruser.representation_integration_flag as master_representation_integration_flag,Masteruser.es_hpd_status as master_es_hpd_status 
            		FROM `users` AS `User` LEFT JOIN `users` AS `Masteruser` 
            		ON (`Masteruser`.`id` = `User`.`master_user_id`) WHERE `User`.`status` = 'A' AND `User`.`id` = " . $user_id;
            $executeSQL = mysqli_query($this->link,$strSQL);
            $arrUserFlags = mysqli_fetch_assoc($executeSQL);
            return $arrUserFlags;
        }
    }

    /**
     * 
     * @param number $user_id
     */
    function getUserStatus($user_id = 0)
    {
        if ($user_id) {
            $strSQL = "SELECT `User`.`username`, `User`.`firstname`, `User`.`lastname`, `User`.`email`, `User`.`status`, `User`.`created`, `User`.`modified` FROM `users` AS `User` WHERE `User`.`status` = 'A' AND `User`.`id` = " . $user_id;
            $executeSQL = mysqli_query($this->link,$strSQL);
            $arrUserStatus = mysqli_fetch_assoc($executeSQL);
            return $arrUserStatus;
        }
    }

    /**
     * 
     * @param number $user_id
     * @param string $flag
     */
    function getUserPropertyCount($user_id = 0, $flag = 'Active')
    {
        if ($user_id) {
            $strSQL = "SELECT COUNT(*) AS `total_property` FROM `properties` AS `Property` INNER JOIN `propertyusers` AS `Propertyuser` ON (`Propertyuser`.`property_id` = `Property`.`id`) WHERE `Propertyuser`.`user_id` = " . $user_id . " AND `Property`.`property_status` = '" . $flag . "' ";
            $executeSQL = mysqli_query($this->link,$strSQL);
            $arrUserPropertyCount = mysqli_fetch_assoc($executeSQL);
            return $arrUserPropertyCount;
        }
    }

    /**
     * 
     * @param number $user_id
     * @param string $flag
     */
    function getUserProperties($user_id = 0, $flag = 'Active')
    {
        if ($user_id) {
            $strSQL = "SELECT `Property`.`id` FROM `properties` AS `Property` INNER JOIN `propertyusers` AS `Propertyuser` ON (`Propertyuser`.`property_id` = `Property`.`id`) WHERE `Propertyuser`.`user_id` = " . $user_id . " AND `Property`.`property_status` = '" . $flag . "' ";
            $executeSQL = mysqli_query($this->link,$strSQL);
            while ($eachRecord = mysqli_fetch_assoc($executeSQL)) {
                $arrUserProperties[] = $eachRecord;
            }
            return $arrUserProperties;
        }
    }

    function getUsers($status = 'A')
    {
        $strSQL = "SELECT `User`.`id`, `User`.`username` FROM `users` AS `User` WHERE `User`.`executive_email_alert_flag` = 'Active' AND `User`.`status` = '" . $status . "' Order By username ASC";
        $executeSQL = mysql_query($strSQL);
        while ($eachRecord = mysql_fetch_assoc($executeSQL)) {
                $arrUsers[$eachRecord['id']] = $eachRecord['username'];
            }
        return $arrUsers;
    }

}

?>