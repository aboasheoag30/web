<?php // owner/_layout/footer.php ?>
  </main>
</div> <!-- shell -->

<div id="toast" class="toast"></div>

<script src="../assets/app.js"></script>
<script src="../assets/owner.js"></script>
<script>
(async function(){
  try{
    const me = await api("/me.php");
    if(!(["owner","staff"]).includes(me.me.role)) { alert("ليس لديك صلاحية"); logout(); return; }

    try{
      const n = await api("/notifications/inbox.php");
      const c = (n.items||[]).length;
      const m = document.getElementById("nav_notifs");
      const d = document.getElementById("nav_notifs_desk");
      if(m) m.textContent = String(c);
      if(d) d.textContent = String(c);
    }catch(_){ }
  }catch(e){ }
})();
</script>

</body>
</html>
