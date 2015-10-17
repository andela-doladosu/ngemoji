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
   * Execute the api patch call
   * 
   * @param  int $id     
   * @param  array $params 
   * @return null         
   */
  public function patch($id, $params)
  {
    return $this->update($id, $param);
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

    return $emoji->save() ? ["msg" => "update successful"] : ["msg" => "Nothing to update"];
  }

}