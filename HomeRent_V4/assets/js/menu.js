/* File: /assets/js/menu.js */
document.addEventListener('DOMContentLoaded',()=>{
 const btn=document.querySelector('.mobile-menu');
 const nav=document.querySelector('nav');
 if(btn&&nav){btn.addEventListener('click',()=>nav.classList.toggle('active'));}
});
