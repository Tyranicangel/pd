<?php

class Query extends Eloquent {

	protected $table = "queries";
	public $timestamps = false;

	protected $guarded = ["id"];
}