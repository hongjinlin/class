function User(username, password) {
	this.username = username;
	this.password = password;
}
User.prototype.usernameIsEmail = function(){
	var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	this._error = '请输入正确的email';
    return regex.test(this.username);
}
User.prototype.passwordCheck = function(){
	if(this.password.length < 6){
		this._error = '请输入6位以上密码'
		return false;
	}
}
User.prototype.getError = function(){
	return this._error;
}