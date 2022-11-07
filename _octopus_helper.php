<?php
// @formatter:off
// phpcs:ignoreFile

namespace Illuminate\Contracts\Auth {
    interface Guard
    {
        /**
         * Get the currently authenticated user.
         *
         * @return \App\Models\User|null
         */
        public function user();
    }
}

namespace Illuminate\Http {
    interface Request {
        /**
         * Get the currently authenticated user.
         *
         * @return \App\Models\User|null
         */
        public function user();
    }
}
