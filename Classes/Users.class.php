<?php 


class Users extends Database {

   	function __construct(){ }

   	// Получаем список всех пользователей зарегестрированных на сайте
   	function getListUsers(): ?array {}

   	// Получаем один профиль указанного пользователя 
   	function getUserProfile($idmail): ?array {}

   	// Обновляем существующую информацию пользователя 
   	function updateUserProfile(int $userid, array $userparams): bool {}

   	// Удаляем пользователя из базы данных (Не удаляем ставим галочку, что он больне не активный)
   	function deleteUserProfile(int $userid): bool {}

   	// Блокируем пользователя в базе данных
   	function blockUserProfile()int $userid): bool {}
}