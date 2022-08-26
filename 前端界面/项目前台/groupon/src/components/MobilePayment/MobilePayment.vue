<!--  -->
<template>
  <div></div>
</template>

<script>
export default {
  data() {
    return {};
  },
  mounted() {
      const div=document.createElement('div');
      div.innerHTML=this.$route.query.htmls;
      document.body.appendChild(div);
      document.forms[0].submit();
  }
};
</script>
<style scoped>
/* @import url(); 引入css类 */
</style>
