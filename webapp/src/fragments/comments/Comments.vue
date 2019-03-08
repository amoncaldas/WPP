<template>
  <div>
    <box class="comment" background="white" :no-top-border="true" v-if="comments">
      <div slot="header">
        <h3>{{$t('comments.comments')}}</h3>
      </div>
      <div slot="content">
        <br>
        <v-form ref="form" v-if="open">
          <v-layout row wrap class="comment-box">
            <v-flex sm1>
              <img v-if="$store.getters.isAuthenticated" :src="commenterAvatar" :alt="$store.getters.user.displayName" :title="$store.getters.user.displayName">
              <img v-else src="https://www.gravatar.com/avatar/HASH">
            </v-flex>
            <v-flex sm11 class="comment-input">
              <v-textarea class="" type="text" autofocus hide-details
                :label="$t('comments.yourComment')" box
                :title="$t('comments.yourComment')"
                v-model="resource.content"
                :required="true">
              </v-textarea>
            </v-flex>
          </v-layout>
          <div class="comment-btns">
            <v-btn color="secondary" left v-if="$store.getters.isAuthenticated" @click.native="submit">{{$t('global.send')}}</v-btn>
            <v-btn color="secondary" v-else @click.native="openAuthenticatio()">{{$t('comments.autneticateAndSend')}}</v-btn>
          </div>
          <vue-recaptcha :sitekey="$store.getters.options.recaptcha_site_key"
            ref="recaptcha"
            size="invisible"
            @verify="onCaptchaVerified"
            @expired="onCaptchaExpired">
          </vue-recaptcha>
          <logint-or-register v-if="showLogintOrRegister" :after-login="afterLogin" :persistent="false"></logint-or-register>
        </v-form>
        <div v-else>
          <h4>{{$t('comments.commentsAreClosed')}}</h4>
          <div v-if="comments.length > 0">
            <hr>
            <br>
          </div>
        </div>
        <template v-for="comment in comments">
          <div :key="comment.id">
            <v-layout row wrap>
              <v-flex sm1>
                <img :src="comment.author_avatar_urls[48]" :alt="comment.author_name" :title="comment.author_name">
              </v-flex>
              <v-flex sm10>
                <div>
                  <span class="author-name" :style="{color: theme.dark}">{{comment.author_name}}</span>
                </div>
                <div v-html="getContent(comment)"></div>
                <div class="comment-date">
                  {{when(comment.date) | capitalize}}
                </div>
              </v-flex>
            </v-layout>
          </div>
        </template>
      </div>
      <div slot="footer">
        <div class="text-xs-left" v-if="totalPages >  1">
          <v-pagination
            v-model="currentPage"
            :length="totalPages">
          </v-pagination>
          <br><br>
        </div>
      </div>
    </box>

  </div>
</template>

<script src="./comments.js"></script>
<style scoped src="./comments.css"></style>
