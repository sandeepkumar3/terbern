<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AppError
 *
 * @author sandeep
 */
class AppError extends ErrorHandler {
    function _outputMessage($template) {
        $this->controller->render($template, 'resreplayout');
        $this->controller->afterFilter();
        echo $this->controller->output;
    }
}