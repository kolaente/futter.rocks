@script
<script>
    Alpine.data('csvImporter', () => ({
        csvFile: null,
        csvContent: null,
        headers: [],
        selectedColumn: null,
        uniqueCounts: {},
        fileName: '',
        availableGroups: @json($availableGroups),
        valueMappings: {},
        valueToGroupMap: {},
        clearStoredMappings: false,

        init() {
            this.$watch('csvFile', (file) => {
                if (!file) return
                this.fileName = file.name
                this.parseCSV(file)
            })
        },

        parseCSV(file) {
            const reader = new FileReader()
            reader.onload = (e) => {
                this.csvContent = e.target.result
                const lines = this.csvContent.split('\n')

                if (lines.length > 0) {
                    // Parse headers (first line)
                    this.headers = this.parseCSVLine(lines[0])
                    this.selectedColumn = null
                    this.uniqueCounts = {}
                    this.valueMappings = {}
                    this.valueToGroupMap = {}
                }
            }
            reader.readAsText(file)
        },

        parseCSVLine(line) {
            // Simple CSV parsing (handles quoted values with commas)
            const result = []
            let current = ''
            let inQuotes = false

            for (let i = 0; i < line.length; i++) {
                const char = line[i]

                if (char === '"') {
                    inQuotes = !inQuotes
                } else if (char === ',' && !inQuotes) {
                    result.push(current.trim())
                    current = ''
                } else {
                    current += char
                }
            }

            if (current.trim()) {
                result.push(current.trim())
            }

            return result
        },

        countUniqueValues() {
            if (!this.csvContent || !this.selectedColumn) return

            const lines = this.csvContent.split('\n')
            const columnIndex = this.headers.indexOf(this.selectedColumn)
            const uniqueValues = {}

            // Start from index 1 to skip header row
            for (let i = 1; i < lines.length; i++) {
                if (!lines[i].trim()) continue // Skip empty lines

                const values = this.parseCSVLine(lines[i])
                if (values.length <= columnIndex) continue

                const value = values[columnIndex].trim()
                if (!value) continue

                uniqueValues[value] = (uniqueValues[value] || 0) + 1
            }

            this.uniqueCounts = uniqueValues

            // Initialize mappings
            this.valueMappings = {}
            this.valueToGroupMap = {}

            // Initialize value to group mapping
            Object.keys(uniqueValues).forEach(value => {
                this.valueToGroupMap[value] = ''
            })
        },

        updateMapping(groupId, count, groupName) {
            // If this value was previously mapped to a group, decrement that group's count
            const previousGroupId = this.valueToGroupMap[groupName]
            if (previousGroupId) {
                this.valueMappings[previousGroupId] -= count
            }

            // Store the new group mapping for this value
            this.valueToGroupMap[groupName] = groupId
            if (groupId) {
                this.valueMappings[groupId] = (this.valueMappings[groupId] || 0) + count
            }
        },

        clear() {
            this.csvFile = null
            this.headers = []
            this.selectedColumn = null
            this.uniqueCounts = {}
            this.valueMappings = {}
            this.valueToGroupMap = {}
        },

        submitCounts() {
            // Prepare data for the server
            $dispatch('submit-unique-group-counts', {
                counts: this.uniqueCounts,
                column: this.selectedColumn,
                mappings: this.valueMappings,
                clearStoredMappings: this.clearStoredMappings,
            })

            // Close the modal by clicking the close button in the modal footer
            const modalContainer = this.$root.closest('[x-ref="modalContainer"]')
            if (modalContainer) {
                const closeButton = modalContainer.querySelector('.fi-modal-footer .fi-modal-footer-actions button')
                if (closeButton) {
                    closeButton.click()
                }
            }
        },
    }))
</script>
@endscript

<div
    x-data="csvImporter"
    class="space-y-4"
>
    <p class="text-sm text-gray-700">
        {{ __('Upload the CSV exported from Campflow and select the column that lists the participant groups. We will import the number of participants for each group.') }}
    </p>
    <div class="mb-4">
        <template x-if="!csvFile">
            <div>
                <input
                    type="file"
                    id="csv-file"
                    accept=".csv"
                    class="hidden"
                    x-ref="fileInput"
                    x-on:change="csvFile = $event.target.files[0]"
                >
                <x-button
                    type="button"
                    x-on:click="$refs.fileInput.click()"
                >
                    {{ __('Select CSV File') }}
                </x-button>
            </div>
        </template>

        <template x-if="csvFile">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded flex items-center">
                <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="font-medium">{{ __('File selected') }}: <span x-text="fileName"></span></p>
                    <button type="button" class="text-sm underline mt-1" x-on:click="clear()">
                        {{ __('Change file') }}
                    </button>
                </div>
            </div>
        </template>
    </div>

    <template x-if="headers.length > 0">
        <div class="space-y-4">
            <div>
                <label for="column-select"
                       class="block text-sm font-medium text-gray-700">{{ __('Select participant group column') }}</label>
                <select
                    id="column-select"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                    x-model="selectedColumn"
                    x-on:change="countUniqueValues()"
                >
                    <option value="">{{ __('-- Select participant group column --') }}</option>
                    <template x-for="header in headers" :key="header">
                        <option x-text="header" :value="header"></option>
                    </template>
                </select>
            </div>

            <template x-if="selectedColumn && Object.keys(uniqueCounts).length > 0">
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">{{ __('Map Values to Participant Groups') }}</h3>
                    <div class="bg-gray-50 p-3 rounded-md border border-gray-200 max-h-72 overflow-y-auto">
                        <table class="min-w-full">
                            <thead>
                            <tr>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider py-2 px-3">{{ __('Value') }}</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider py-2 px-3">{{ __('Count') }}</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider py-2 px-3">{{ __('Map to Group') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template x-for="(count, value) in uniqueCounts" :key="value">
                                <tr>
                                    <td class="py-2 px-3 text-sm" x-text="value"></td>
                                    <td class="py-2 px-3 text-sm" x-text="count"></td>
                                    <td class="py-2 px-3 text-sm">
                                        <select
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm"
                                            x-model="valueToGroupMap[value]"
                                            x-on:change="updateMapping($event.target.value, count, value)"
                                        >
                                            <option value="">{{ __('-- Select Group --') }}</option>
                                            <template x-for="group in availableGroups" :key="group.id">
                                                <option :value="group.id" x-text="group.title"></option>
                                            </template>
                                        </select>
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <div class="flex items-center mb-3">
                            <input
                                type="checkbox"
                                id="clear-stored-mappings"
                                class="rounded border-gray-300 text-primary focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 mr-2"
                                x-model="clearStoredMappings"
                            >
                            <label for="clear-stored-mappings" class="text-sm text-gray-700">
                                {{ __('Clear already stored mappings') }}
                            </label>
                        </div>

                        <x-button
                            type="button"
                            x-on:click="submitCounts()"
                        >
                            {{ __('Submit Mappings') }}
                        </x-button>
                    </div>
                </div>
            </template>
        </div>
    </template>
</div>
