<?php
namespace App\Controller;
use App\Controller\AppController;

class CustomerNotesController extends AppController
{
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent("GYMFunction");
	}
	
	public function customerNotesList(){
            $session = $this->request->session()->read("User");
            $loggedUser = $this->GYMFunction->get_user_detail($session['id']);
            
            switch($session["role_name"]){
                CASE "administrator" :
                    $data = $this->CustomerNotes->find("all")
                        ->contain(["NoteFor","CreatedBy","AssociatedLicensee","GymClass"])
                        ->select([
                            'CustomerNotes.note_title',
                            'CustomerNotes.comment',
                            'CustomerNotes.created_at',
                            'GymClass.name',
                            'NoteFor.first_name',
                            'NoteFor.last_name',
                            'CreatedBy.first_name',
                            'CreatedBy.last_name',
                            'AssociatedLicensee.first_name',
                            'AssociatedLicensee.last_name',
                        ])
                        ->hydrate(false)->toArray();
                    $this->GYMFunction->pre($data);
                break;

                CASE "staff_member" :
                CASE "licensee" :
                    $data = $this->CustomerNotes->find("all")
                        ->contain(["NoteFor","CreatedBy","AssociatedLicensee","GymClass"])
                        ->select([
                            'CustomerNotes.note_title',
                            'CustomerNotes.id',
                            'CustomerNotes.comment',
                            'CustomerNotes.created_at',
                            'GymClass.name',
                            'NoteFor.first_name',
                            'NoteFor.last_name',
                            'CreatedBy.first_name',
                            'CreatedBy.last_name',
                            'CreatedBy.id',
                            'AssociatedLicensee.first_name',
                            'AssociatedLicensee.last_name',
                        ])
                        ->hydrate(false)->toArray();
                    //$this->GYMFunction->pre($data);
                    //$data = $this->CustomerNotes->find("all")->where(["associated_licensee"=>$loggedUser['associated_licensee']])->hydrate(false)->toArray();
                break;
            
                /*
                CASE "member" :
                        $class_ids = $this->CustomerNotes->get_class_by_member($session["id"]);
                        if(!empty($class_ids)){
                            $data = $this->CustomerNotes->find("all")->where(["OR"=>[["class_id IN"=>$class_ids],["notice_for"=>"member"],["notice_for"=>"all"]]])->hydrate(false)->toArray();
                        }else{
                            $data = $this->GymNotiCustomerNotesce->find("all")->where(["OR"=>[["notice_for"=>"member"],["notice_for"=>"all"]]])->hydrate(false)->toArray();
                        }
                break;
                
                CASE "accountant" : 
                        $data = $this->CustomerNotes->find("all")->where(["OR"=>[["notice_for"=>"accountant"],["notice_for"=>"all"]]])->hydrate(false)->toArray();
                break;	
                
                */	
            }
            $this->set("data",$data);
	}
        
	public function addCustomerNotes(){
            $session = $this->request->session()->read("User");
            
            $loggedUser = $this->GYMFunction->get_user_detail($session['id']);
            
            $this->set("edit",false);		
            
            $classes = $this->CustomerNotes->GymClass->find("list",["keyField"=>"id","valueField"=>"name"]);
            $this->set("classes",$classes);
            
            $note_for = $this->CustomerNotes->GymMember->find("list",["keyField"=>"id","valueField"=>"name"])->where(["role_name"=>"member","role_id"=>4,"associated_licensee"=>$loggedUser["associated_licensee"]]);
            $note_for = $note_for->select(["id","name"=>$note_for->func()->concat(["first_name"=>"literal"," ","last_name"=>"literal"])])->hydrate(false)->toArray();
            
            $this->set("note_for",$note_for);
            
            if($this->request->is("post")){
                $row = $this->CustomerNotes->newEntity();			
                $this->request->data["start_date"] = date("Y-m-d",strtotime($this->request->data["start_date"]));		
                $this->request->data["end_date"] = date("Y-m-d",strtotime($this->request->data["end_date"]));		
                $this->request->data["created_by"] = $session["id"];	
                $this->request->data["associated_licensee"] = $loggedUser["associated_licensee"];

                /*SANITIZATION*/
                $this->request->data["comment"] = $this->GYMFunction->sanitize_string($this->request->data["comment"]);
                /*SANITIZATION*/

                $row = $this->CustomerNotes->patchEntity($row,$this->request->data);
                if($this->CustomerNotes->save($row)){				
                    $this->Flash->success(__("Success! Record Successfully Saved."));
                    return $this->redirect(["action"=>"customerNotesList"]);			
                }else{
                    $this->Flash->error(__("Error! Record Not Saved.Please Try Again."));
                }
            }		
	}
        
	public function editNotice($pid)
	{	
		$this->set("edit",true);		
		$row = $this->GymNotice->get($pid);
		$this->set("data",$row->toArray());
		
		$classes = $this->GymNotice->ClassSchedule->find("list",["keyField"=>"id","valueField"=>"class_name"]);
		$this->set("classes",$classes);
		
		if($this->request->is("post"))
		{
			
			$this->request->data["start_date"] = date("Y-m-d",strtotime($this->request->data["start_date"]));		
			$this->request->data["end_date"] = date("Y-m-d",strtotime($this->request->data["end_date"]));
			
			/*SANITIZATION*/
			$this->request->data["comment"] = $this->GYMFunction->sanitize_string($this->request->data["comment"]);
			/*SANITIZATION*/
			
			$row = $this->GymNotice->patchEntity($row,$this->request->data);
			if($this->GymNotice->save($row))
			{
				$this->Flash->success(__("Success! Record Successfully Updated."));
				return $this->redirect(["action"=>"noticeList"]);
			}else{
				$this->Flash->error(__("Error! Record Not Updated.Please Try Again."));
			}
		}
		$this->render("addNotice");
	}
	
	public function deleteNotice($did)
	{
		$row = $this->GymNotice->get($did);
		if($this->GymNotice->delete($row))
		{
			$this->Flash->success(__("Success! Record Deleted Successfully Updated."));
			return $this->redirect(["action"=>"noticeList"]); 
		} 		
	}
        
        public function isAuthorized($user){
            return parent::isAuthorizedCustom($user);
	}
	
	
}