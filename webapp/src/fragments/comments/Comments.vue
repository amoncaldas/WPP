<template>
  <div>
    <box tag="section" class="comment" background="white" :no-top-border="true" v-if="comments">
      <div slot="header">
        <h2>{{$t('comments.comments')}}</h2>
      </div>
      <div slot="content">
        <br>
        <v-form ref="form" v-if="open">
          <v-layout row wrap class="comment-box">
            <v-flex xs2 sm1>
              <img class="max-100" :src="commenterAvatar" :alt="commenterName" :title="commenterName">
            </v-flex>
            <v-flex xs10 sm11 class="comment-input">
              <v-textarea class="" type="text" autofocus hide-details auto-grow counter
                :label="$t('comments.yourComment')" box
                :title="$t('comments.yourComment')"
                v-model="resource.content"
                :required="true">
              </v-textarea>
            </v-flex>
          </v-layout>
          <div class="comment-btns" v-if="ready">
            <v-btn color="secondary" left v-if="$store.getters.isAuthenticated" @click.native="submit">{{$t('global.send')}}</v-btn>
            <v-btn color="secondary" v-else @click.native="openAuthentication()">{{$t('comments.autneticateAndSend')}}</v-btn>
          </div>
          <vue-recaptcha v-if="$store.getters.options.recaptcha_site_key" :sitekey="$store.getters.options.recaptcha_site_key"
            ref="recaptcha"
            size="invisible"
            @verify="onCaptchaVerified"
            @expired="onCaptchaExpired">
          </vue-recaptcha>
          <login-or-register @closed="showLoginOrRegister = false" v-if="showLoginOrRegister" :after-login="afterLogin" :persistent="false"></login-or-register>
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
              <v-flex xs2 sm1>
                <img class="max-100" :src="commentAvatar(comment)" :alt="commentName(comment)" :title="commentName(comment)">
              </v-flex>
              <v-flex xs10 sm11>
                <div>
                  <span class="author-name" :style="{color: $vuetify.theme.dark}">{{commentName(comment)}}</span>
                </div>
                <div v-html="getContent(comment)"></div>
                <div class="comment-date">
                  <time :datetime="comment.date">{{humanizedDate(comment.date)}}</time>
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
