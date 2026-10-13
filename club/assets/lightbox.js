/* NOVA Club — agrandissement des photos (clavier, glisser sur mobile) */
(function(){
  var groups=document.querySelectorAll('[data-lightbox]');if(!groups.length)return;
  var lb=document.createElement('div');lb.className='c-lightbox';lb.setAttribute('role','dialog');lb.setAttribute('aria-modal','true');lb.setAttribute('aria-label','Image agrandie');
  lb.innerHTML='<img alt=""><p class="c-lb-cap"></p><button class="c-lb-close" type="button">Fermer</button><button class="c-lb-prev" type="button" aria-label="Précédente">←</button><button class="c-lb-next" type="button" aria-label="Suivante">→</button>';
  document.body.appendChild(lb);
  var img=lb.querySelector('img'),cap=lb.querySelector('.c-lb-cap'),links=[],i=0,last=null;
  function show(n){i=(n+links.length)%links.length;img.src=links[i].href;var t=links[i].getAttribute('data-caption')||'';img.alt=t;cap.textContent=t;}
  function open(list,n){links=list;last=document.activeElement;show(n);lb.classList.add('on');lb.querySelector('.c-lb-close').focus();}
  function close(){lb.classList.remove('on');img.src='';if(last)last.focus();}
  [].forEach.call(groups,function(g){var list=[].slice.call(g.querySelectorAll('a[data-zoom]'));
    list.forEach(function(a,n){a.addEventListener('click',function(e){e.preventDefault();open(list,n);});});});
  lb.querySelector('.c-lb-close').onclick=close;
  lb.querySelector('.c-lb-prev').onclick=function(){show(i-1)};lb.querySelector('.c-lb-next').onclick=function(){show(i+1)};
  lb.addEventListener('click',function(e){if(e.target===lb)close();});
  document.addEventListener('keydown',function(e){if(!lb.classList.contains('on'))return;if(e.key==='Escape')close();if(e.key==='ArrowLeft')show(i-1);if(e.key==='ArrowRight')show(i+1);});
  var x0=null;lb.addEventListener('touchstart',function(e){x0=e.touches[0].clientX},{passive:true});
  lb.addEventListener('touchend',function(e){if(x0===null)return;var dx=e.changedTouches[0].clientX-x0;if(Math.abs(dx)>50)show(i+(dx<0?1:-1));x0=null;});
})();
