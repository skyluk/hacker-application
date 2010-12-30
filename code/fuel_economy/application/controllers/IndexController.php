<?php

class IndexController extends Zend_Controller_Action
{
    protected $mapper;

    public function init() {
        $this->mapper = new Application_Model_EntryMapper();
    }

    public function indexAction() {

        $form = $this->getForm();

        if( $this->getRequest()->isPost() ) {

            if( $form->isValid($_POST) ) {
                $values = $form->getValues();

                $this->view->vals = $values;
                
                /* this method doesn't work as Zend is using the PDO
                 * library which does not invoke mysql_connect().
                 * PDO does, however, provide escaping for strings
                 * which seems adequate for this type of operation.
                 */
                //$description = mysql_real_escape_string($values['description']);

                $entry = new Application_Model_Entry();

                $entry->setId((int) $values['id'])
                      ->setDescription($values['description'])
                      //->setDescription($description)
                      ->setMiles((int) $values['miles'])
                      ->setCost((float) $values['fuelcost'])
                      ->setMpg((int) $values['mpg']);

                if( strlen($values['drivetime']) > 0 )
                    $entry->setDrivetime($values['drivetime']);

                //$mapper = new Application_Model_EntryMapper();

                $this->mapper->save($entry);

                // tell the index script to display the form
                $this->view->showform = 1;
                $this->view->selectedentry = $entry;
            }
            else {
                $this->view->vals = 'form not valid';
            }
        }

        // creates an instance of the mapper
        //$mapper = new Application_Model_EntryMapper();

        // retrieves all items from the entries
        // table and adds them to the view.
        $this->view->entries = $this->mapper->fetchAll();

        // get a form object and add it to the view
        $this->view->form = $form;
    }


    /**
     * This function creates and returns a new Zend_Form object.  The form
     * is initialized with fields for displaying details of an route entry.
     */
    public function getForm() {
        $form = new Zend_Form();
        $form->setMethod('post');

        $id = $form->createElement('hidden', 'id');

        $description = $form->createElement('text', 'description', array('label' => 'Description'));
        $description->addValidator('StringLength', true, array(1, 128))
                    ->setRequired(true);

        $miles = $form->createElement('text', 'miles', array('label' => 'Miles'));
        $miles->addValidator('digits', false)
              ->setRequired(true);

        $drivetime = $form->createElement('text', 'drivetime', array('label' => 'Drive time'));
        $drivetime->addValidator('digits', false)
                  ->setRequired(false);

        $cost = $form->createElement('text', 'fuelcost', array('label' => 'Fuel cost'));
        $cost->addValidator('float', false)
             ->setRequired(true);
        
        $mpg = $form->createElement('text', 'mpg', array('label' => 'MPG'));
        $mpg->addValidator('digits', false)
            ->setRequired(true);

        $form->addElement($id);
        $form->addElement($description);
        $form->addElement($drivetime);
        $form->addElement($miles);
        $form->addElement($cost);
        $form->addElement($mpg);
        $form->addElement('submit', 'save', array('label' => 'Save'));

        return $form;
    }

    /**
     * This function is responsible for handling an ajax request from the index script.
     */
    public function getDataAction() {
        // the following two lines ensure that there
        // is no unwanted html in the response.
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $request = $this->getRequest();
        $id = $request->getParam('id');

        //$mapper = new Application_Model_EntryMapper();
        $search = new Application_Model_Entry();
        $this->mapper->find($id, $search);

        if( $search->getId() > 0 ) {
            // the search was successful

            $json = array('id' => $search->getId(),
                          'description' => $search->getDescription(),
                          'drivetime' => $search->getDrivetime(),
                          'miles' => $search->getMiles(),
                          'fuelcost' => $search->getCost(),
                          'mpg' => $search->getMpg());

            $responsebody = json_encode($json);

            // configure and append the body to the response
            $this->getResponse()
                 ->setHeader('Content-Type', 'text/json')
                 ->appendBody($responsebody);
        }
        else {
            // the search was unsuccessful, the item could not be located
            
            $this->getResponse()->setException(new Exception("The entry with id = $id could not be found."));
        }
    }
}

