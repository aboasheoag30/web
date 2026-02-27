// assets/owner.js
(function(){
  function $(id){ return document.getElementById(id); }

  const overlay = $("drawerOverlay");
  const drawer  = $("drawer");
  const openBtn = $("openDrawerBtn");
  const closeBtn= $("closeDrawerBtn");

  function open(){
    if(!overlay || !drawer) return;
    overlay.classList.add("open");
    drawer.classList.add("open");
  }
  function close(){
    if(!overlay || !drawer) return;
    overlay.classList.remove("open");
    drawer.classList.remove("open");
  }

  if(openBtn) openBtn.addEventListener("click", open);
  if(closeBtn) closeBtn.addEventListener("click", close);
  if(overlay) overlay.addEventListener("click", close);

  document.addEventListener("click", (e)=>{
    const a = e.target && e.target.closest ? e.target.closest("a[data-close-drawer='1']") : null;
    if(a) close();
  });

  window.ownerToast = function(message, kind){
    const t = $("toast");
    if(!t) return;
    t.textContent = message || "";
    t.classList.add("show");
    if(kind === "err") t.style.borderColor = "rgba(239,68,68,.35)";
    else if(kind === "ok") t.style.borderColor = "rgba(22,163,74,.35)";
    else t.style.borderColor = "rgba(15,23,42,.12)";
    clearTimeout(window.__toastTimer);
    window.__toastTimer = setTimeout(()=> t.classList.remove("show"), 3200);
  };
})();
