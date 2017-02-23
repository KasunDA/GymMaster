<?php
namespace App\Controller;
use Cake\App\Controller;

class StaffMembersController extends AppController
{
    public function initialize(){
        parent::initialize();
        $this->loadComponent("GYMFunction");	
    }

    public function staffList(){
        $session = $this->request->session()->read("User");
        //echo $session['id'];die;
        switch($session["role_name"]){
            CASE "administrator" :
                $data = $this->StaffMembers->GymMember
                ->find()->contain(['GymRoles'])
                ->where([
                    "GymMember.role_name"=>"staff_member"
                    ])
                ->select(['GymRoles.name'])->select($this->StaffMembers->GymMember)
                ->hydrate(false)->toArray();
            break;

            CASE "franchise" :
                $data = $this->StaffMembers->GymMember
                ->find()->contain(['GymRoles'])
                ->where([
                    "GymMember.role_name"=>"staff_member",
                    "GymMember.created_by"=>$session['id']
                    ])
                ->select(['GymRoles.name'])->select($this->StaffMembers->GymMember)
                ->hydrate(false)->toArray();
                //var_dump($this->StaffMembers->getDataSource()->showLog());die;
            break;		
        }
        //$data = $this->StaffMembers->GymMember->find()->contain(['GymRoles'])->where(["GymMember.role_name"=>"staff_member"])->select(['GymRoles.name'])->select($this->StaffMembers->GymMember)->hydrate(false)->toArray();
        $this->set("data",$data);
    }

    public function addStaff(){
        $session = $this->request->session()->read("User");
        $this->set("session",$session);
        $this->set("edit",false);
        $this->set("title",__("Add Staff Member"));

        $roles = $this->StaffMembers->GymMember->GymRoles->find("list",["keyField"=>"id","valueField"=>"name"])->hydrate(false)->toArray();
        $this->set("roles",$roles);
        
        if($session['role_id'] == 1){
            $franchises = $this->StaffMembers->GymMember->find("list",["keyField"=>"id","valueField"=>"first_name"])->where(["GymMember.role_id"=>2, "GymMember.activated"=>1])->hydrate(false)->toArray();
            $this->set("franchises",$franchises);
        }

        $specialization = $this->StaffMembers->GymMember->Specialization->find("list",["keyField"=>"id","valueField"=>"name"])->hydrate(false)->toArray();
        $this->set("specialization",$specialization);
        

        if($this->request->is("post")){
            $staff = $this->StaffMembers->GymMember->newEntity();

            $image = $this->GYMFunction->uploadImage($this->request->data['image']);
            $this->request->data['image'] = (!empty($image)) ? $image : "logo.png";
            $this->request->data['birth_date'] = date("Y-m-d",strtotime($this->request->data['birth_date']));
            $this->request->data['created_date'] = date("Y-m-d");
            $this->request->data['s_specialization'] = json_encode($this->request->data['s_specialization']);
            $this->request->data["role_name"]="staff_member";
            $staff = $this->StaffMembers->GymMember->patchEntity($staff,$this->request->data);

            if($this->StaffMembers->GymMember->save($staff)){
                $this->Flash->success(__("Success! Record Successfully Saved."));
                return $this->redirect(["action"=>"staffList"]);
            }else{				
                if($staff->errors()){	
                    foreach($staff->errors() as $error){
                        foreach($staff as $key=>$value){
                            $this->Flash->error(__($value));
                        }						
                    }
                }
            }
        }
    }

    public function editStaff($id){
        $session = $this->request->session()->read("User");
        //echo '<pre>';print_r($session);die;

        $this->set("edit",true);
        $this->set("title",__("Edit Staff Member"));

        $data = $this->StaffMembers->GymMember->get($id)->toArray();
        $roles = $this->StaffMembers->GymMember->GymRoles->find("list",["keyField"=>"id","valueField"=>"name"])->hydrate(false)->toArray();
        $specialization = $this->StaffMembers->GymMember->Specialization->find("list",["keyField"=>"id","valueField"=>"name"])->hydrate(false)->toArray();

        $this->set("specialization",$specialization);
        $this->set("roles",$roles);		
        $this->set("data",$data);
        $this->render("AddStaff");

        if($this->request->is("post")){
            $row = $this->StaffMembers->GymMember->get($id);
            $this->request->data['birth_date'] = date("Y-m-d",strtotime($this->request->data['birth_date']));
            $this->request->data['s_specialization'] = json_encode($this->request->data['s_specialization']);
            $image = $this->GYMFunction->uploadImage($this->request->data['image']);
            if($image != ""){
                $this->request->data['image'] = $image;
            }else{
                unset($this->request->data['image']);
            }
            /* $this->request->data['image'] = (!empty($image)) ? $image : "logo.png";*/
            $update = $this->StaffMembers->GymMember->patchEntity($row,$this->request->data);
            if($this->StaffMembers->GymMember->save($update)){
                $this->Flash->success(__("Success! Record Updated Successfully."));
                return $this->redirect(["action"=>"staffList"]);
            }else{				
                if($update->errors()){	
                    foreach($update->errors() as $error){
                        foreach($error as $key=>$value){
                            $this->Flash->error(__($value));
                        }						
                    }
                }
            }
        }
    }

    public function deleteStaff($id){
        $row = $this->StaffMembers->GymMember->get($id);
        if($this->StaffMembers->GymMember->delete($row)){
            $this->Flash->success(__("Success! Staff Member Deleted Successfully."));
            return $this->redirect($this->referer());
        }
    }

    public function isAuthorized($user)
    {
        $role_name = $user["role_name"];
        $curr_action = $this->request->action;	
        $members_actions = ["staffList"];
        $staff_acc_actions = ["staffList"];
        switch($role_name){			
            CASE "member":
                    if(in_array($curr_action,$members_actions))
                    {return true;}else{return false;}
            break;

            CASE "staff_member":
                    if(in_array($curr_action,$staff_acc_actions))
                    {return true;}else{ return false;}
            break;

            CASE "accountant":
                    if(in_array($curr_action,$staff_acc_actions))
                    {return true;}else{return false;}
            break;
        }

        return parent::isAuthorized($user);
    }
}