(function(){
  var toggle=document.getElementById('sidebarToggle');
  if(toggle){toggle.addEventListener('click',function(){document.querySelector('.admin-sidebar').classList.toggle('show');});}
})();
