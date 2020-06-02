<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use App\Company;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Facades\Excel;
use KubAT\PhpSimple\HtmlDomParser;

class DataExport
{

    public function export($url)
    {
        $result = [];
        $result = $this->process($url);
        if(!empty($result)){
            $company_detail['cin'] = isset($result['Corporate Identification Number']) ? $result['Corporate Identification Number'] : 'not found';
            $company_detail['registration_number'] = isset($result['Registration Number']) ? $result['Registration Number'] : 'not found';
            $company_detail['company_name'] = isset($result['Company Name']) ? $result['Company Name'] : 'not found';
            $company_detail['company_name'] = isset($result['Company Name']) ? $result['Company Name'] : 'not found';
            $company_detail['status'] = isset($result['Company Status']) ? $result['Company Status'] : 'not found';
            $company_detail['build_year'] = isset($result['Age (Date of Incorporation)']) ? $result['Age (Date of Incorporation)'] : 'not found';
            $company_detail['email'] = isset($result['Email Address']) ? $result['Email Address'] : 'not found';
            $company_detail['other_details'] = json_encode($result);

            Company::updateOrCreate(
               ['cin' => $company_detail['cin']],
               $company_detail
            );
            echo "Parsing is done and save into db";
        }
        else{

            echo "Parsing not done";
        }

    }

    public function process($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
        }
        curl_close($ch);
        if (isset($error_msg)) {
            print_r($error_msg);
        }

        $dom = HtmlDomParser::str_get_html($response);

       if (empty($dom->find('h1.not-found-title'))) {

            $data = [];
            foreach ($dom->find('tr') as $trData) {
                if($trData->find('td', 0)){
                    $page_title_array = explode("<br>", $trData->find('td', 1)->innertext);
                    $string = strip_tags($page_title_array[0]);
                    $content = str_replace("&nbsp;", "", $string);
                    $content = html_entity_decode($content);
                    $data[strip_tags($trData->find('td', 0)->innertext)] = $content;
                }
            }
            return $data;
        }
    }

    
}