let img = document.getElementById('curriculam');

img.addEventListener('mouseover', function() {
    img.style.border = 'solid 2px #0000FF';
});

img.addEventListener('mouseleave', function(){
    img.style.border = '0px';
});