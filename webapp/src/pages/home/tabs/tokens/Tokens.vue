<template>
  <div>
    <v-layout v-if="showAlert" row wrap>
        <v-flex xs12 lg12>
          <v-alert :value="true" type="warning" style="color:black" >
            {{ $t('home_tokens.havingProblems') }}
            <v-icon color="primary" @click="showAlert = false" style="cursor: pointer; float:right" :title="$t('home_tokens.closeTokenIssueWarning')">close</v-icon>
          </v-alert>
          <br><br>
        </v-flex>
    </v-layout>
    <v-data-table :no-data-text="emptyTableMessage" :items="resources" hide-actions class="table-container">
      <template slot="headers" slot-scope="props">
        <tr>
          <th :class="['column sortable desc asc', pagination.descending ? 'desc' : 'asc', 'token_name' === pagination.sortBy ? 'active' : '']"
            @click="changeSort('token_name')">
            <v-icon small>arrow_upward</v-icon>
            {{ $t('home_tokens.name') }}
          </th>
          <th :class="['column sortable', pagination.descending ? 'desc' : 'asc', 'key' === pagination.sortBy ? 'active' : '']"
            @click="changeSort('key')">
            <v-icon small>arrow_upward</v-icon>
            {{ $t('home_tokens.key') }}
          </th>
          <th :class="['column ', pagination.descending ? 'desc' : 'asc', 'is_valid' === pagination.sortBy ? 'active' : '']"
            @click="changeSort('is_valid')">
            <v-icon small>arrow_upward</v-icon>
            {{ $t('home_tokens.isValid') }}
          </th>
          <th class="hidden-sm-and-down column">
            {{ $t('home_tokens.quota') }}
          </th>
          <th class="column">
            {{ $t('home_tokens.actions') }}
          </th>
        </tr>
      </template>

      <template slot="items" slot-scope="props">
        <td style="text-align:center">{{ props.item.token_name }}</td>
        <td :title="$t('home_tokens.singleClickToCopyToken')" @click="copyTokenKey(props.item.key)" style="text-align:center; cursor:copy">{{ props.item.key }}</td>
        <td style="text-align:center">{{ props.item.is_valid? $t('global.yes'): $t('global.no') }}</td>
        <td style="text-align:center" class="hidden-sm-and-down">
          {{ props.item.quota.quota_max > 0? props.item.quota.quota_remaining + '/' +props.item.quota.quota_max : $t('home_tokens.notUsedYet') }}
        </td>
        <td style="text-align:center">
          <v-btn icon @click="showUsage(props.item)" :title="$t('home_tokens.seeUsage')">
            <v-icon color="dark">trending_up</v-icon>
          </v-btn>
          <v-btn icon @click="confirmAndDestroy(props.item)" :title="$t('home_tokens.revoke')">
            <v-icon color="pink">delete</v-icon>
          </v-btn>
        </td>
      </template>
    </v-data-table>
    <br><br><br><br>
    <v-layout row wrap>
      <v-flex xs12 lg12  >
        <transition enter-active-class="animated fadeIn" leave-active-class="animated fadeOut" mode="out-in">
          <box background="white" v-if="showTokenUsage" custom-class="chart-box" noTopBorder
            :closable="$vuetify.breakpoint.mdAndUp"
            v-model="showTokenUsage"
            @closed="showTokenUsage = false" >
            <span class="headline" slot="header">{{$t('home_tokens.seeUsage')}}</span>
            <div slot="content">
              <v-layout row wrap>
                <div>
                  <br>
                  <v-icon color="primary">vpn_key</v-icon> <span>: {{inspectingToken.token_name}} </span> <br>
                  <span> {{ $t('home_tokens.quota')}}: {{inspectingToken.quota.quota_remaining + '/' + inspectingToken.quota.quota_max }}</span>
                  <br><br>
                </div>
              </v-layout>
              <v-layout row wrap>
                <v-flex xs6>
                  <date-picker :on-change="onUsageDateRangeChange" :model.sync="usageStartDate" :label="$t('home_tokens.from')" ></date-picker>
                </v-flex>
                <v-flex xs6>
                  <date-picker :min="usageStartDate" :disabled="!usageStartDate" :model.sync="usageEndDate" :label="$t('home_tokens.to')"
                    :on-change="onUsageDateRangeChange"
                    :max="usageTodayDate" >
                  </date-picker>
                </v-flex>
              </v-layout>
              <chart-wrapper class="pr-20">
                <line-chart :labels="usageLabels" :datasets="usageDatasets"></line-chart>
              </chart-wrapper>
            </div>
          </box>
        </transition>
        <transition enter-active-class="animated fadeInDown" leave-active-class="animated fadeOutUp" mode="out-in">
          <p v-if="!showTokenUsage">{{ $t('home_tokens.selectATokenToSeeItsUsage') }}</p>
        </transition>
      </v-flex>
    </v-layout>
    <br><br>

    <v-form class="token-form" ref="form" @keyup.native.enter="save">
      <v-layout row wrap>
        <v-flex xs12 lg12>
          <span class="headline">{{ $t('home_tokens.newToken') }}</span>
          <v-layout row wrap>
            <v-flex xs6>
              <v-select required :items="tokenTypes" v-model="resource.api" item-value="id" item-text="name" :label="$t('home_tokens.tokenType')"
                single-line></v-select>
            </v-flex>
            <v-flex xs6>
              <v-text-field :label="$t('home_tokens.tokenName')" v-model="resource.name" required></v-text-field>
            </v-flex>
          </v-layout>
          <v-layout row wrap>
            <v-spacer></v-spacer>
            <v-btn large right color="secondary" @click="save">{{ $t('home_tokens.createToken') }}
              <v-icon right>send</v-icon>
            </v-btn>
          </v-layout>
        </v-flex>
      </v-layout>

    </v-form>
  </div>
</template>

<script src="./tokens.js"></script>


