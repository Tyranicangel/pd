<?php

class Areas extends Eloquent {

	protected $table = "arealist";
	public $timestamps = false;

	protected $guarded = ["id"];
}