<?php
// FoodClass.php
// 5-31-11  rlb
// Food class definition for weight tracker, wt.php, wt_member.php

require_once("wt_include.php");

class Food 
{
	// food table fields
	private $id;				// food id in database
	private $name;				// food name
	private $description;		// food description
	private $owner;				// food owner
	private $serving_size;		// serving size
	private $serving_units;		// serving size measurement units (gms, oz, cups, tbls, tsp)
	private $ingredient_flag;	// ingredient flag, indicates whether food element, or food component
	
	private $ingredients;	// 2 dim array of "ingredient_id", "ingredient_name", "servings", if they exist
				
	// nutrition info, if it exists
	private $calories;	
	private $points;
	private $fat_grams;
	private $carb_grams;
	private $protein_grams;
	private $fiber_grams;
	
	public function __construct($setArray="")
	{
		$this->ingredients = array();
		if ($setArray)
		{
			$this->setAll($setArray);
		}
	}
	public function setAll($setArray)
	{
		// allows for any properties to be set using associative array
		// format: "id"=>100, "name"=>"Chicken Soup"
		foreach($setArray as $var=>$value)
		{
			$this->$var = $value;
		}
	}
	public function getId()
	{
		return $this->id;
	}
	public function setId($id)
	{
		$this->id = $id;
	}
	public function getName()
	{
		return $this->name;
	}
	public function setName($name)
	{
		$this->name = $name;
	}
	public function getDescription()
	{
		return $this->description;
	}
	public function setDescription($desc)
	{
		$this->description = $desc;
	}
	public function getOwner()
	{
		return $this->owner;
	}
	public function setOwner($owner)
	{
		$this->owner = $owner;
	}
	public function getIngredientFlag()
	{
		return $this->ingredient_flag;
	}
	public function getServingSize()
	{
		return $this->serving_size;
	}
	public function getServingUnits()
	{
		return $this->serving_units;
	}
	public function setServing($serving)
	{
		// $serving is assoc array of "serving_size" and "serving_units"
		$this->serving_size = $serving['serving_size'];
		$this->serving_units = $serving['serving_units'];
	}
	public function getNutrition($mysqli, &$errCode)
	{
		// if no ingredients, return nutrients
		// otherwise, calculate from ingredients
		$nutrients = array();		
		if (!$this->ingredients)
		{
			if (isset($this->calories)) $nutrients['calories'] = $this->calories;
			if (isset($this->points)) 	$nutrients['points'] = $this->points;
			if (isset($this->fat_grams)) 		$nutrients['fat'] = $this->fat_grams;
			if (isset($this->carb_grams))	$nutrients['carbs'] = $this->carb_grams;
			if (isset($this->protein_grams)) 	$nutrients['protein'] = $this->protein_grams;
			if (isset($this->fiber_grams)) 	$nutrients['fiber'] = $this->fiber_grams;	
		}
		else
		{
			$errCode = "";
			$nutrients = calcNutrients($mysqli, $this->id, $errCode);
			if ($errCode)
				return false;
		}
		return $nutrients;
	}
	public function setNutrition($nutrients)
	{
		// $nutrients needs to be an assoc array containing
		// some combo of "calories", "points", "fat", "carbs", "protein", "fiber"
		extract($nutrients);
		if (isset($calories)) 	$this->calories = $calories;
		if (isset($points))		$this->points = $points;
		if (isset($fat)) 		$this->fat_grams = $fat;
		if (isset($carbs))		$this->carb_grams = $carbs;
		if (isset($protein)) 	$this->protein_grams = $protein;
		if (isset($fiber)) 		$this->fiber_grams = $fiber;
	}
	public function getIngredients()
	{
		return $this->ingredients;
	}
	public function setIngredients($ingreds)
	{
		$this->ingredients = $ingreds;
	}
}
?>