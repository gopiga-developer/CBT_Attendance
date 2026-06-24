<?php

class FileManager
{
    protected function readJson($_file_name)
    {
        
        if (!file_exists($_file_name)) {
        return [];
        }

        return json_decode(file_get_contents($_file_name),true);
    }

    protected function writeJson($_file_name,$_data) {
        file_put_contents($_file_name,json_encode($_data,JSON_PRETTY_PRINT));
    }
}