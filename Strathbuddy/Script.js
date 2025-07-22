const RegisterButton=document.getElementById('RegistrationButton')
const LoginButton=document.getElementById('LoginButton')
const RegisterForm=document.getElementById('Registration')
const LoginForm=document.getElementById('Login')

RegisterButton.addEventListener('click',function(){
RegisterForm.style.display="none";
LoginForm.style.display="block";
})

LoginButton.addEventListener('click',function(){
LoginForm.style.display="block";
LoginForm.style.display="none";
})