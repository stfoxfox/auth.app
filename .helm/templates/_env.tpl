{{- define "app_envs" }}
- name: DATABASE_URL
  value: "postgresql://{{ pluck .Values.global.env .Values.psql.user | first | default .Values.psql.user._default }}:{{ pluck .Values.global.env .Values.psql.password | first | default .Values.psql.password._default }}@{{ pluck .Values.global.env .Values.psql.host | first | default .Values.psql.host._default }}:{{ pluck .Values.global.env .Values.psql.port | first | default .Values.psql.port._default }}/{{ pluck .Values.global.env .Values.psql.db | first | default .Values.psql.db._default }}"
- name: JWT_SECRET_KEY
  value: "{{ pluck .Values.global.env .Values.app.jwt_secret_key | first | default .Values.app.jwt_secret_key._default }}"
- name: JWT_PUBLIC_KEY
  value: "{{ pluck .Values.global.env .Values.app.jwt_public_key | first | default .Values.app.jwt_public_key._default }}"
- name: JWT_PASSPHRASE
  value: "{{ pluck .Values.global.env .Values.app.jwt_passphrase | first | default .Values.app.jwt_passphrase._default }}"
- name: LOCATE
  value: "{{ pluck .Values.global.env .Values.app.locate | first | default .Values.app.locate._default }}"
- name: SECURITY_SALT
  value: "{{ pluck .Values.global.env .Values.app.security_salt | first | default .Values.app.security_salt._default }}"
- name: FBID
  value: "{{ pluck .Values.global.env .Values.app.fbid | first | default .Values.app.fbid._default }}"
- name: FBSECRET
  value: "{{ pluck .Values.global.env .Values.app.fbsecret | first | default .Values.app.fbsecret._default }}"
- name: COOCKIE_DOMAIN
{{- if eq .Values.global.env "production" }}
  value: {{ pluck .Values.global.env .Values.app.site_domain | first | default .Values.app.site_domain._default }}
{{- else }}
  value: {{ printf (pluck .Values.global.env .Values.app.site_domain | first | default .Values.app.site_domain._default) .Values.global.env }}
{{- end }}
- name: AUTENTICATION_COOKIE
  value: "{{ pluck .Values.global.env .Values.app.authentification_cookie_name | first | default .Values.app.authentification_cookie_name._default }}"
- name: TOKEN_LIFETIME
  value: "{{ pluck .Values.global.env .Values.app.token_life_time | first | default .Values.app.token_life_time._default }}"
- name: LENGTH_PASSWORD
  value: "{{ pluck .Values.global.env .Values.app.length_password | first | default .Values.app.length_password._default }}"
- name: FROM_EMAIL
  value: "{{ pluck .Values.global.env .Values.app.from_email | first | default .Values.app.from_email._default }}"
- name: SITE_DOMAIN
{{- if eq .Values.global.env "production" }}
  value: {{ pluck .Values.global.env .Values.app.site_domain | first | default .Values.app.site_domain._default }}
{{- else }}
  value: {{ printf (pluck .Values.global.env .Values.app.site_domain | first | default .Values.app.site_domain._default) .Values.global.env }}
{{- end }}
- name: COOKIE_LANGUAGE
  value: "{{ pluck .Values.global.env .Values.app.cookie_language | first | default .Values.app.cookie_language._default }}"
- name: PRIVATE_API_ENDPOINT
  value: "{{ pluck .Values.global.env .Values.app.private_api_endpoint | first | default .Values.app.private_api_endpoint._default }}"
- name: MOBILE_API_ENDPOINT
  value: "{{ pluck .Values.global.env .Values.app.mobile_api_endpoint | first | default .Values.app.mobile_api_endpoint._default }}"
- name: REDIS_DNS
  value: "{{ pluck .Values.global.env .Values.redis.dns | first | default .Values.redis.dns._default }}"
- name: REDIS_PORT
  value: "{{ pluck .Values.global.env .Values.redis.port | first | default .Values.redis.port._default }}"
- name: REDIS_DATABASE
  value: "{{ pluck .Values.global.env .Values.redis.database | first | default .Values.redis.database._default }}"
- name: MAILER_URL
  value: "{{ pluck .Values.global.env .Values.app.mailer_url | first | default .Values.app.mailer_url._default }}"
{{- end }}

