<?php

class imageController
{
    private image $image;

    public function __construct()
    {
        $this->image = new image();
    }

    /**
     * get one image
     * @throws Exception
     */
    public function get($id): void
    {
        $image = $this->image;

        $data = $image->getRow($id);

        if ($data) {

            header('Expires: 0');
            header('Pragma: public');
            header('Access-Control-Allow-Origin: *');
            header('Content-Transfer-Encoding: binary');
            header('Content-Description: File Transfer');
            header("Content-Type: {$data['type']}; charset=UTF-8");
            header("Content-Disposition: Inline; filename={$data['name']}");
            header('Cache-Control: must-re_validate, post-check=0, pre-check=0');

            $file = "data:";
            $file .= $data['type'];
            $file .= ";charset=utf8;base64,";
            $file .= base64_encode($data['image']);

            ob_clean();
            flush();

            readfile($file);

        } else {
            http_response_code(405);
            echo json_encode(
                array(
                    'message' => 'image non disponible',
                    'status' => 405
                ),
                JSON_THROW_ON_ERROR);
        }
    }
}