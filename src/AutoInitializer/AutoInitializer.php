<?php namespace Devine\AutoInitializer;
use ReflectionClass;
use Exception;
class AutoInitializer{
  public function build($className){
    $reflection = new ReflectionClass($className);
    if (! $reflection->isInstantiable()){
      throw new Exception("Not Instantiable");
    }
    $constructor = $reflection->getConstructor();
    if(is_null($constructor)) return new $className;
    $parameters = $constructor->getParameters();
    if(count($parameters) == 0) return new $className;
    $dependencies = $this->getDependencies($parameters);
    return $reflection->newInstanceArgs($dependencies);
  }

  public function getDependencies($parameters){
    $dependencies = [];
    foreach($parameters as $parameter){
      $dependency = $parameter->getClass();
      if(is_null($dependency)){
        $dependencies[] = $this->resolveNonClass($parameter);
      }else{
        $dependencies[] = $this->resolveClass($dependency);
      }
    }
    return $dependencies;
  }

  public function resolveNonClass($parameter){
    if($parameter->isDefaultValueAvailable()){
      return $parameter->getDefaultValue();
    }else{
      throw new Exception("Default Not Prvided!");
    }
  }

  public function resolveClass($depedency){
    return $this->build($depedency->name);
  }
}