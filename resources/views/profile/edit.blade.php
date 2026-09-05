<x-admin-layout :title="'Mon profil'">
  <div class="topbar">
    <div>
      <h1>Mon profil</h1>
    </div>
  </div>

  <div class="panel" style="max-width:480px;">
    @include('profile.partials.update-profile-information-form')
  </div>

  <div class="panel" style="max-width:480px;">
    @include('profile.partials.update-password-form')
  </div>
</x-admin-layout>
