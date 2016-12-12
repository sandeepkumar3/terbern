<?php

require_once 'vendors/executivesummary/configs/config.php';

class ReportConfig extends config
{

    /**
     * 
     * @param number $user_id
     */
    function getUserConfig($user_id = 0)
    {
        
    }

    /**
     * 
     * @param number $report_id
     */
    function getConfig($report_id = 0)
    {
        if ($report_id) {
            $strSQL = "SELECT `ReportingConfig`.`label`, `ReportingConfig`.`name`, `ReportingConfig`.`title`, `ReportingConfig`.`subject`, `ReportingConfig`.`header_text`, `ReportingConfig`.`footer_text`,ReportingConfig.representation_section FROM `reporting_configs` AS `ReportingConfig` WHERE `ReportingConfig`.`id` = " . $report_id;
            $executeSQL = mysqli_query($this->link, $strSQL);
            $arrReportsConfig = mysqli_fetch_assoc($executeSQL);
            return $arrReportsConfig;
        } else {
            return 0;
        }
    }

}

?>