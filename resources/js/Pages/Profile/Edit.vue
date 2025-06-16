<template>
  <div class="min-h-screen bg-gray-50 flex flex-col" data-v-app>
    <Navbar />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Main Profile Content -->
      <div class="flex gap-8">
        <!-- Left Column - Profile Info -->
        <div class="w-80">
          <div class="text-center mb-6">
            <div class="relative w-24 h-24 mx-auto mb-4">
              <div v-if="user.profile_photo_url" class="w-full h-full rounded-full overflow-hidden">
                <img :src="user.profile_photo_url" alt="Profile photo" class="w-full h-full object-cover" />
              </div>
              <div v-else
                class="w-full h-full bg-gray-200 rounded-full flex items-center justify-center text-gray-400 text-2xl">
                {{ getInitials(user.name) }}
              </div>
              <button @click="$refs.photoInput.click()"
                class="absolute bottom-0 right-0 bg-gray-900 text-white p-1.5 rounded-full hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </button>
              <input ref="photoInput" type="file" class="hidden" accept="image/*" @change="updatePhoto" />
            </div>
            <h2 class="text-xl font-medium mb-1">{{ user.name }}</h2>
            <p class="text-sm text-gray-500 mb-4">{{ user.title || 'Professional Title' }}</p>
            <button @click="showEditModal = true"
              class="w-full bg-gray-900 text-white py-2 px-4 rounded text-sm font-medium hover:bg-gray-800">
              Edit Profile
            </button>
          </div>

          <div class="space-y-4">
            <div class="flex items-center text-sm text-gray-600">
              <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              {{ user.location || 'Location not set' }}
            </div>
            <div class="flex items-center text-sm text-gray-600">
              <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              {{ user.email }}
            </div>
            <div class="flex items-center text-sm text-gray-600">
              <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              {{ user.phone || 'Phone not set' }}
            </div>
          </div>

          <!-- Skills -->
          <div class="mt-8">
            <h3 class="font-medium mb-3">Skills I Can Teach</h3>
            <div class="flex flex-wrap gap-2">
              <span v-for="skill in teachingSkills" :key="skill.id"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                {{ skill.name }}
              </span>
            </div>
          </div>

          <div class="mt-6">
            <h3 class="font-medium mb-3">Skills I Want to Learn</h3>
            <div class="flex flex-wrap gap-2">
              <span v-for="skill in learningSkills" :key="skill.id"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                {{ skill.name }}
              </span>
            </div>
          </div>
        </div>

        <!-- Right Column - Requests and Messages -->
        <div class="flex-1">
          <!-- Tabs -->
          <div class="border-b border-gray-200 mb-8">
            <div class="flex space-x-8">
              <button @click="activeTab = 'requests'" :class="{
                'border-b-2 border-gray-900 text-gray-900': activeTab === 'requests',
                'border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'requests'
              }" class="py-4 px-1 text-sm font-medium">
                Swap Requests
              </button>
              <button @click="activeTab = 'messages'" :class="{
                'border-b-2 border-gray-900 text-gray-900': activeTab === 'messages',
                'border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'messages'
              }" class="py-4 px-1 text-sm font-medium relative">
                Messages
                <span v-if="unreadMessages > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                  {{ unreadMessages }}
                </span>
              </button>
            </div>
          </div>

          <!-- Swap Requests Tab Content -->
          <div v-if="activeTab === 'requests'">
            <!-- Incoming Requests -->
            <div class="space-y-4 w-full max-w-4xl">
              <div v-for="request in incomingRequests" :key="request.id"
                class="group relative flex items-center justify-between p-6 bg-white border rounded-lg w-full">
  <!-- Remove (X) button -->
  <button @click="removeRequest(request.id, 'incoming')"
    class="absolute top-2 right-2 p-1 text-gray-400 bg-gray-100 rounded-full shadow-sm z-50 hover:bg-red-500 hover:text-white transition"
    title="Remove">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
    </svg>
  </button>
                <!-- Show ratings given by you and received for this request -->
                <div v-if="request.ratings && request.ratings.length > 0" class="flex flex-col text-xs mb-2">
                  <div v-for="rating in request.ratings" :key="rating.id" class="flex items-center gap-2">
                    <!-- <span v-if="rating.rater_id === props.user.id">You rated: <b>{{ rating.score }}</b> ⭐</span> -->
                    <!-- <span v-else-if="rating.rated_user_id === props.user.id">Rated by other: <b>{{ rating.score }}</b> ⭐</span> -->
                  </div>
                </div>
                <div class="flex items-center space-x-4">
                  <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-400">
                    <!-- {{ getInitials(request.user.name) }} -->
                      <img :src="request.user.profile_photo_url" alt="">
                  </div>
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ request.user.name }}</div>
                    <div class="text-sm text-gray-500">
                      Wants to learn: {{ request.skill_wanted }}
                      <br />
                      Offers to teach: {{ request.skill_offered }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                      Received {{ formatDate(request.created_at) }}
                    </div>
                  </div>
                </div>
                <div class="flex items-center space-x-4 ml-8">
                  <template v-if="request.status === 'Pending'">
                    <button @click="acceptRequest(request.id)"
                      class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded-md hover:bg-green-200">
                      Accept
                    </button>
                    <button @click="declineRequest(request.id)"
                      class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded-md hover:bg-red-200">
                      Decline
                    </button>
                  </template>
                  <template v-else-if="request.status === 'Accepted'">
                    <Link v-if="request.user && request.user.id" :href="route('messages.show', request.user.id)"
                      class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 mr-4">
                      Message
                    </Link>
                    <button v-if="!hasRatedByMe(request)" @click="openRatingModal(request)"
                      class="px-3 py-1 text-sm bg-yellow-100 text-yellow-700 rounded-md hover:bg-yellow-200">
                      Rate
                    </button>
                    <div class="text-sm text-green-600">Accepted</div>
                  </template>
                  <template v-else>
                    <div class="text-sm" :class="{
                      'text-red-600': request.status === 'Declined',
                      'text-gray-600': request.status === 'Cancelled'
                    }">
                      {{ request.status }}
                    </div>
                  </template>
                </div>
              </div>
            </div>

            <!-- Outgoing Requests -->
            <h3 class="font-medium text-lg mt-8 mb-4">Outgoing Requests</h3>
            <div class="space-y-4 w-full max-w-4xl">
              <div v-for="request in outgoingRequests" :key="request.id"
                class="group relative p-6 bg-white border rounded-lg w-full">
                <!-- Remove (X) button -->
                <button @click="removeRequest(request.id, 'outgoing')"
                  class="absolute top-2 right-2 p-1 text-gray-400 bg-gray-100 rounded-full shadow-sm z-50 hover:bg-red-500 hover:text-white transition"
                  title="Remove">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
                <button v-if="request.status === 'Pending'" @click="cancelRequest(request.id)"
                  class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity p-1 text-gray-700 bg-gray-100 rounded-full shadow-sm z-50 hover:bg-gray-200">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
                <div class="flex items-center space-x-4">
                  <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-400">
                    <!-- {{ getInitials(request.recipient.name) }} -->
                    <img :src="request.recipient.profile_photo_url" alt="" class="w-full h-full object-cover rounded-full">
                  </div>
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ request.recipient.name }}</div>
                    <div class="text-sm text-gray-500">
                      You want to learn: {{ request.skill_wanted }}
                      <br />
                      You offer to teach: {{ request.skill_offered }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                      Sent {{ formatDate(request.created_at) }}
                    </div>
                  </div>
                </div>
                <div class="mt-2 text-sm" :class="{
                  'text-yellow-600': request.status === 'Pending',
                  'text-green-600': request.status === 'Accepted',
                  'text-red-600': request.status === 'Declined',
                  'text-gray-600': request.status === 'Cancelled'
                }">
                  {{ request.status }}
                  <Link v-if="request.status === 'Accepted'" :href="route('messages.show', request.recipient.id)"
                    class="ml-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200">
                  Message
                  </Link>
                  <button v-if="request.status === 'Accepted' && !hasRatedByMe(request)" @click="openRatingModal(request)"
                    class="ml-2 px-3 py-1 bg-yellow-100 text-yellow-700 rounded-md hover:bg-yellow-200">
                    Rate
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Messages Tab Content -->
          <div v-if="activeTab === 'messages'">
            <div class="space-y-4 w-full" style="max-width: 90%;">
              <div v-for="conversation in conversations" :key="conversation.id"
                class="group relative flex flex-col p-6 bg-white border rounded-lg w-full cursor-pointer"
                style="min-width: 540px; width: 90%;"
                @click="openConversation(conversation)">
                <div class="flex items-center justify-between w-full mb-3">
                  <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
                      <img v-if="conversation.other_user.profile_photo_url" :src="conversation.other_user.profile_photo_url" :alt="conversation.other_user.name" class="w-full h-full object-cover" />
                      <span v-else class="text-gray-400">{{ getInitials(conversation.other_user.name) }}</span>
                    </div>
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ conversation.other_user.name }}</div>
                      <div class="text-xs text-gray-400">
                        {{ formatDate(conversation.last_message.created_at) }}
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Message preview -->
                <div class="text-sm text-gray-500 mb-3 pl-14">
                  {{ conversation.last_message.content }}
                </div>
                
                <!-- Skills being swapped -->
                <div class="flex items-center pl-14">
                  <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-blue-100 text-blue-700">
                    {{ conversation.skill_offered }} ↔ {{ conversation.skill_wanted }}
                  </span>
                </div>
              </div>

              <div v-if="conversations.length === 0" class="p-8 text-center text-gray-500">
                No messages yet. Start a conversation by accepting a swap request.
              </div>
            </div>

            <div v-if="hasMoreConversations" class="p-4 border-t border-gray-200 text-center">
              <button @click="loadMoreConversations" class="text-sm font-medium text-gray-900 hover:text-gray-700 focus:outline-none">
                Load More Messages
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Edit Profile Modal -->
      <Modal :show="showEditModal" @close="showEditModal = false" max-width="2xl">
        <template #default>
          <div class="p-6">
            <h2 class="text-lg font-medium mb-4">Edit Profile</h2>

            <form @submit.prevent="saveProfile" class="space-y-6">
              <!-- Photo Upload -->
              <div class="flex items-center space-x-4 mb-6">
                <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-100">
                  <img v-if="user.profile_photo_url" :src="user.profile_photo_url" alt="Profile photo"
                    class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-xl">
                    {{ getInitials(user.name) }}
                  </div>
                </div>
                <div>
                  <button type="button" @click="$refs.modalPhotoInput.click()"
                    class="bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Change Photo
                  </button>
                  <input ref="modalPhotoInput" type="file" class="hidden" accept="image/*" @change="updatePhoto" />
                </div>
              </div>

              <!-- Form Fields -->
              <div class="space-y-4">
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                  <input id="name" v-model="form.name" type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 sm:text-sm" />
                </div>

                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                  <input id="email" v-model="form.email" type="email"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 sm:text-sm" />
                </div>

                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                  <input id="phone" v-model="form.phone" type="tel"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 sm:text-sm" />
                </div>

                <div>
                  <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                  <input id="location" v-model="form.location" type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 sm:text-sm" />
                </div>

                <div>
                  <label for="title" class="block text-sm font-medium text-gray-700">Professional Title</label>
                  <input id="title" v-model="form.title" type="text"
                    placeholder="e.g. Full Stack Developer, UI Designer, Music Teacher"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 sm:text-sm" />
                </div>
              </div>

              <!-- Skills I Can Teach -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Skills I Can Teach</label>
                <div class="flex flex-wrap gap-2 mb-3">
                  <span v-for="skill in form.teachingSkills" :key="skill.name"
                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-sm font-medium bg-gray-100 text-gray-800">
                    {{ skill.name }}
                    <button @click="removeTeachingSkill(skill)" type="button" class="text-gray-500 hover:text-gray-700">
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                  </span>
                </div>
                <div class="flex gap-2">
                  <input v-model="newTeachingSkill" type="text" placeholder="Add a skill you can teach..."
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900"
                    @keyup.enter="addTeachingSkill">
                  <select v-model="newTeachingCategory"
                    class="w-40 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900">
                    <option value="">Category</option>
                    <option v-for="category in categories" :key="category" :value="category">
                      {{ category }}
                    </option>
                  </select>
                  <button @click="addTeachingSkill" type="button"
                    class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                    Add
                  </button>
                </div>
              </div>

              <!-- Skills I Want to Learn -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Skills I Want to Learn</label>
                <div class="flex flex-wrap gap-2 mb-3">
                  <span v-for="skill in form.learningSkills" :key="skill.name"
                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-sm font-medium bg-gray-100 text-gray-800">
                    {{ skill.name }}
                    <button @click="removeLearningSkill(skill)" type="button" class="text-gray-500 hover:text-gray-700">
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                  </span>
                </div>
                <div class="flex gap-2">
                  <input v-model="newLearningSkill" type="text" placeholder="Add a skill you want to learn..."
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900"
                    @keyup.enter="addLearningSkill">
                  <select v-model="newLearningCategory"
                    class="w-40 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900">
                    <option value="">Category</option>
                    <option v-for="category in categories" :key="category" :value="category">
                      {{ category }}
                    </option>
                  </select>
                  <button @click="addLearningSkill" type="button"
                    class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                    Add
                  </button>
                </div>
              </div>

              <!-- Form Actions -->
              <div class="flex justify-end space-x-3 pt-6">
                <button type="button" @click="showEditModal = false"
                  class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                  Cancel
                </button>
                <button type="submit"
                  class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                  Save Changes
                </button>
              </div>
            </form>
          </div>
        </template>
      </Modal>
    </div>
    
    <!-- Footer -->
    <footer class="bg-gray-100 py-3 text-xs text-gray-500 mt-auto">
      <div class="container mx-auto px-4 flex justify-between items-center">
        <div> 2025 SkillSwap. All rights reserved.</div>
        <div class="flex space-x-4">
          <Link :href="route('about')" class="hover:underline">About</Link>
          <Link :href="route('terms')" class="hover:underline">Terms</Link>
          <Link :href="route('privacy')" class="hover:underline">Privacy</Link>
          <Link :href="route('contact')" class="hover:underline">Contact</Link>
        </div>
      </div>
    </footer>
  </div>
  <!-- Rating Modal -->
  <Modal :show="showRatingModal" @close="closeRatingModal">
    <div class="p-6">
      <h2 class="text-lg font-medium mb-4">
  Rate your experience with
  <span v-if="selectedRequest">
    <!-- Show the other user's name -->
    {{ props.user.id === selectedRequest.user?.id ? selectedRequest.recipient?.name : selectedRequest.user?.name }}
  </span>
</h2>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
        <RatingStars v-model="ratingForm.score" :readonly="false" :show-value="false" />
      </div>

      <!-- <div class="mb-4">
        <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Comment (Optional)</label>
        <textarea v-model="ratingForm.comment" rows="3"
          class="w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring focus:ring-gray-900 focus:ring-opacity-50"
          placeholder="Share your experience..."></textarea>
      </div> -->

      <div class="flex justify-end space-x-3">
        <button @click="closeRatingModal"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
          Cancel
        </button>
        <button @click="submitRating" :disabled="!ratingForm.score || ratingForm.processing"
          class="px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-md hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed">
          Submit Rating
        </button>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import Navbar from '@/Components/Navbar.vue';
import RatingStars from '@/Components/RatingStars.vue';
import axios from 'axios';

// Props definition
const props = defineProps({
  user: Object,
  teachingSkills: Array,
  learningSkills: Array,
  incomingRequests: Array,
  outgoingRequests: Array,
  initialConversations: {
    type: Array,
    default: () => []
  }
});
console.log("lll",props.incomingRequests);
// State management
const activeTab = ref('requests');
const conversations = ref(props.initialConversations || []);
const currentPage = ref(1);
const lastPage = ref(1);
const isLoading = ref(false);
const unreadMessages = ref(0);
const hasMoreConversations = computed(() => currentPage.value < lastPage.value);

// Calculate unread messages count
const calculateUnreadMessages = () => {
  // Only count messages that were sent by others (not by the current user)
  unreadMessages.value = conversations.value.filter(conversation => 
    conversation.last_message && 
    !conversation.last_message.is_read && 
    conversation.last_message.user_id !== props.user.id
  ).length;
  
  console.log('Unread messages:', unreadMessages.value);
};

// Calculate unread messages and fetch conversations on component mount
onMounted(() => {
  // Immediately calculate unread messages based on initial data
  calculateUnreadMessages();
  
  // Then fetch fresh data from the server
  fetchConversations();
  
  // Listen for new messages via Echo (real-time updates)
  if (window.Echo) {
    console.log(`Setting up Echo listeners for user ID: ${props.user.id}`);
    

    
    // IMPORTANT: Listen for all MessageSent events on the user's private channel
    window.Echo.private(`user.${props.user.id}`)
      .listen('MessageSent', (e) => {
        console.log('🔔 MessageSent event received on user channel:', e);
        
        // If I'm the recipient of this message, show the notification badge
        if (e.message && e.message.recipient_id === props.user.id && e.message.user_id !== props.user.id) {
          console.log('📩 I am the recipient - showing notification badge');
          
          // Increment unread count immediately for the badge
          unreadMessages.value += 1;
          console.log(`Unread messages count increased to: ${unreadMessages.value}`);
          
          // Refresh conversations in the background
          fetchConversations();
        }
      });
    
    // Also listen directly on the Pusher instance for broader event capture
    const pusher = window.Echo.connector.pusher;
    
    // Listen on the general channel for any message events
    pusher.subscribe('private-user.' + props.user.id)
      .bind('MessageSent', (data) => {
        console.log('📨 Message event from Pusher subscription:', data);
        
        // Make sure we only count messages sent TO us, not FROM us
        if (data.message && 
            data.message.recipient_id === props.user.id && 
            data.message.user_id !== props.user.id) {
          console.log('📬 Incoming message detected - updating badge');
          unreadMessages.value += 1;
          // Force reactive update
          unreadMessages.value = parseInt(unreadMessages.value);
          console.log(`Badge count updated to: ${unreadMessages.value}`);
        }
      });
      
    // Listen on the chat channel as well for redundancy
    pusher.subscribe('private-chat.' + props.user.id)
      .bind('MessageSent', (data) => {
        console.log('💬 Message received on chat channel:', data);
        
        if (data.message && 
            data.message.recipient_id === props.user.id && 
            data.message.user_id !== props.user.id) {
          console.log('🔴 Updating badge from chat channel');
          unreadMessages.value += 1;
          // Force reactive update
          unreadMessages.value = parseInt(unreadMessages.value);
        }
      });
      
    // Debug connection status
    pusher.connection.bind('connected', () => {
      console.log('✅ Pusher connected successfully');
    });
    
    pusher.connection.bind('error', (err) => {
      console.error('❌ Pusher connection error:', err);
    });
  }
});
const showEditModal = ref(false);
const showRatingModal = ref(false);
const selectedRequest = ref(null);
const incomingRequests = ref(props.incomingRequests || []);
const outgoingRequests = ref(props.outgoingRequests || []);
const newTeachingSkill = ref('');
const newTeachingCategory = ref('');
const newLearningSkill = ref('');
const newLearningCategory = ref('');

// Helper to check if the logged-in user has rated the other party for this request
function hasRatedByMe(request) {
  if (!request.ratings || !props.user) return false;
  return request.ratings.some(rating => rating.rater_id === props.user.id);
}

// Form state
const form = useForm({
  name: props.user?.name || '',
  email: props.user?.email || '',
  phone: props.user?.phone || '',
  location: props.user?.location || '',
  title: props.user?.title || '',
  teachingSkills: props.teachingSkills || [],
  learningSkills: props.learningSkills || []
});

const ratingForm = useForm({
  score: 0,
  comment: ''
});

// Categories
const categories = [
  'Design',
  'Development',
  'Music',
  'Language',
  'Business',
  'Lifestyle',
  'Photography',
  'Education'
];

// Remove request from backend and UI
const removeRequest = async (requestId, type) => {
  try {
    const response = await axios.delete(route('api.swap-requests.destroy', requestId));
    if (response.data.success) {
      if (type === 'incoming') {
        incomingRequests.value = incomingRequests.value.filter(r => r.id !== requestId);
      } else if (type === 'outgoing') {
        outgoingRequests.value = outgoingRequests.value.filter(r => r.id !== requestId);
      }
    } else {
      alert(response.data.message || 'Failed to remove request.');
    }
  } catch (error) {
    alert('Failed to remove request.');
  }
};

// Functions
const fetchConversations = async (page = 1) => {
  try {
    isLoading.value = true;
    const response = await axios.get(route('messages.conversations'), { params: { page } });
    if (page === 1) {
      conversations.value = response.data || [];
    } else {
      conversations.value = [...conversations.value, ...(response.data || [])];
    }
    
    currentPage.value = page;
    lastPage.value = Math.ceil(response.data.length / 10); // Assuming 10 items per page
    
    // Calculate unread messages after loading conversations
    calculateUnreadMessages();
  } catch (error) {
    console.error('Error fetching conversations:', error);
    if (page === 1) {
      conversations.value = [];
    }
  } finally {
    isLoading.value = false;
  }
};

const loadMoreConversations = () => {
  if (hasMoreConversations.value && !isLoading.value) {
    fetchConversations(currentPage.value + 1);
  }
};

const openConversation = (conversation) => {
  if (conversation?.other_user?.id) {
    // Mark conversation as read before navigating
    if (conversation.last_message && !conversation.last_message.is_read && 
        conversation.last_message.user_id !== props.user.id) {
      // Update local state to reflect message is read
      conversation.last_message.is_read = true;
      // Recalculate unread count
      calculateUnreadMessages();
      
      // Optionally send request to backend to mark as read
      axios.post(route('messages.mark-as-read', conversation.id))
        .catch(error => console.error('Error marking conversation as read:', error));
    }
    
    // Navigate to conversation
    router.visit(route('messages.show', conversation.other_user.id));
  } else {
    alert('User information is missing for this conversation.');
  }
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();

  // If today, show time
  if (date.toDateString() === now.toDateString()) {
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  // If yesterday
  const yesterday = new Date(now);
  yesterday.setDate(yesterday.getDate() - 1);
  if (date.toDateString() === yesterday.toDateString()) {
    return 'Yesterday';
  }

  // If this year, show month and day
  if (date.getFullYear() === now.getFullYear()) {
    return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
  }

  // Otherwise, show full date
  return date.toLocaleDateString();
};

const getInitials = (name) => {
  if (!name) return '';
  return name.split(' ').map(n => n[0]).join('').toUpperCase();
};

const addTeachingSkill = () => {
  if (newTeachingSkill.value.trim() && newTeachingCategory.value) {
    form.teachingSkills.push({
      name: newTeachingSkill.value.trim(),
      category: newTeachingCategory.value
    });
    newTeachingSkill.value = '';
    newTeachingCategory.value = '';
  }
};

const removeTeachingSkill = (skill) => {
  form.teachingSkills = form.teachingSkills.filter(s => s.name !== skill.name);
};

const addLearningSkill = () => {
  if (newLearningSkill.value.trim() && newLearningCategory.value) {
    form.learningSkills.push({
      name: newLearningSkill.value.trim(),
      category: newLearningCategory.value
    });
    newLearningSkill.value = '';
    newLearningCategory.value = '';
  }
};

const removeLearningSkill = (skill) => {
  form.learningSkills = form.learningSkills.filter(s => s.name !== skill.name);
};

const acceptRequest = (requestId) => {
  router.post(route('requests.accept', { swapRequest: requestId }), {}, {
    preserveScroll: true,
    onSuccess: () => {
      incomingRequests.value = incomingRequests.value.filter(r => r.id !== requestId);
      fetchConversations();
    }
  });
};

const declineRequest = (requestId) => {
  router.post(route('requests.decline', { swapRequest: requestId }), {}, {
    preserveScroll: true,
    onSuccess: () => {
      incomingRequests.value = incomingRequests.value.filter(r => r.id !== requestId);
    }
  });
};

const cancelRequest = (requestId) => {
  router.post(route('requests.cancel', { swapRequest: requestId }), {}, {
    preserveScroll: true,
    onSuccess: () => {
      outgoingRequests.value = outgoingRequests.value.filter(r => r.id !== requestId);
    }
  });
};

const saveProfile = () => {
  form.patch(route('profile.update'), {
    onSuccess: () => {
      showEditModal.value = false;
      props.user.name = form.name;
      props.user.email = form.email;
      props.user.phone = form.phone;
      props.user.location = form.location;
      props.user.title = form.title;
    }
  });
};

const openRatingModal = (request) => {
  selectedRequest.value = request;
  ratingForm.score = 0; // Always reset to 0 so user must choose
  ratingForm.comment = '';
  showRatingModal.value = true;
;}

const closeRatingModal = () => {
  showRatingModal.value = false;
  selectedRequest.value = null;
  ratingForm.reset();
};

const submitRating = () => {
  if (!ratingForm.score || ratingForm.processing || !selectedRequest.value) return;

  const requestId = selectedRequest.value.id;
  if (!requestId) return;

  ratingForm.post(`/requests/${requestId}/rate`, {
    preserveScroll: true,
    onSuccess: () => {
      // Mark as rated for both incoming and outgoing
      let request = incomingRequests.value.find(r => r.id === requestId);
      if (!request) {
        request = outgoingRequests.value.find(r => r.id === requestId);
      }
      if (request) {
        request.rated = true;
        // Optionally push the new rating to the ratings array for instant UI feedback
        request.ratings = request.ratings || [];
        request.ratings.push({
          id: Date.now(), // Temporary ID
          rater_id: props.user.id,
          rated_user_id: (props.user.id === request.user?.id ? request.recipient?.id : request.user?.id),
          score: ratingForm.score,
          comment: ratingForm.comment
        });
      }
      closeRatingModal();
    }
  });
};

const updatePhoto = (event) => {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('photo', file);

  router.post(route('profile.photo.update'), formData, {
    preserveScroll: true,
    onSuccess: () => {
      // Reload the page to update the photo
      router.reload();
    },
  });
};

// Watch for tab changes and fetch conversations when needed
watch(activeTab, (newTab) => {
  if (newTab === 'messages') {
    fetchConversations();
  }
});

// Fetch conversations if messages tab is active on mount
onMounted(() => {
  if (activeTab.value === 'messages' && conversations.value.length === 0) {
    fetchConversations();
  }
});
</script>