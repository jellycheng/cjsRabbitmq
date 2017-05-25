<?php
namespace CjsRabbitmq\Factory;

/**
 * Desc: 空对象，什么也不做
 */
class EmptyObj {


	public function __call($method, $args) {

		return null;
	}



}