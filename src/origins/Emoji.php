<?php

namespace Dara\Origins;


class Emoji extends Model
{
  
  /**
   * Execute the api put call
   * 
   * @param  int $id     
   * @param  array $params 
   * @return null         
   */
  public function put($id, $params)
  {
    return $this->update($id, $params);
  }


  /**
   * Save a new emoji
   * @param object $app 
   * @return array
   */
  public function post($app)
  {
    $user = new User();
    $this->name = $app->request->params('name');
    $this->category = $app->request->params('category');
    $this->emoji = $app->request->params('emoji');
    $this->keywords = $app->request->params('keywords');
    date_default_timezone_set('Africa/Lagos');
    $this->date_created = date('Y-m-d H:i:s', time());
    $this->created_by = $user->getUserId($app->request->params('username'));

    return $this->save() ? ["msg" => "Emoji saved succesfully"] : ["msg" => "Unable to save emoji"];

  }

  /**
   * Execute the api patch call
   * 
   * @param  int $id     
   * @param  array $params 
   * @return null         
   */
  public function patch($id, $params)
  {
    return $this->update($id, $params);
  }


  /**
   * Carry out an update using provided parameters
   * 
   * @param  int $id          
   * @param  array $inputParams 
   * @return array              
   */
  public function update($id, $inputParams)
  {
    $emoji = Emoji::find($id);

    $params = $inputParams;

    foreach ($params as $key => $value) {
        $emoji->$key = $value;
    }

    date_default_timezone_set('Africa/Lagos');
    $emoji->date_modified = date('Y-m-d H:i:s', time());

    return $emoji->save() ? ["msg" => "update successful"] : ["msg" => "Nothing to update"];
  }

}