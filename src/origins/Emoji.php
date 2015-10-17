<?php

namespace Dara\Origins;

class Emoji extends Model
{
  
  public function put($id, $params)
  {
    return $this->update($id, $params);
  }

  public function patch($id, $params)
  {
    return $this->update($id, $param);
  }

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