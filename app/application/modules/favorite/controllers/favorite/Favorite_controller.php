<?php 
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Favorite_controller extends MX_Controller {

    public function __construct(){
        parent::__construct ();
        $this->load->model('favorite/favorite/Favorites_model');
    }
    /*
    //Functionality : Function Call Page  favorite
    //Parameters : 
    //Creator : 10/1/2020 nonpawich (petch)
    //Return : String View
    //Return Type : View
    */

    public function index() {
        $nLangResort    = $this->session->userdata("tLangID");
        $tOwner         =  $this->session->userdata('tSesUserCode');
        $aDataList =  $this->Favorites_model->FSaFavGetdataList( $tOwner ,$nLangResort);
        $tData = array(
            'aDataList' => $aDataList,
        );
        $this->load->view('favorite/favorite/wfavorite',$tData);
    }
    
    /*
    //Functionality : Function Del  favorite
    //Parameters : 
    //Creator : 10/1/2020 nonpawich (petch)
    //Return : String View
    //Return Type : View
    */
    public function FsxFavDel(){


            $nCode = $this->input->post('nCode');
           
            $this->Favorites_model->FSxFavDel($nCode);
        
    }

  

}
