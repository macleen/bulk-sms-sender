class Packet {
    static char_table = ['1','a','3','b','c','d','5','e','7','f'];  

    constructor(parent_config) {
        this.init(parent_config);
    }

    init(parent_config) {
        this.config = parent_config;
        this.fixed_packet_parms = this.#init_packet_parms();
        this.packet_container = this.#init_packet_container();
        this.#set_fixed_parms();
    }        

    set_entry(k, v) {
        if (this.fixed_packet_parms.hasOwnProperty(k))
            this.fixed_packet_parms[k] = v;
        else if (this.packet_container.hasOwnProperty(k))     
            this.packet_container[k] = v;
    }

    get_entry(k) {
        if (this.fixed_packet_parms.hasOwnProperty(k))
            return this.fixed_packet_parms[k];
        return this.packet_container.hasOwnProperty(k)
            ? this.packet_container[k] : 'undefined';
    }

    #init_packet_parms() {
        return {
            sender_id: '', 
            dial_code_length: '',
            sending_target_country_code: '',
            full_country_name: '',
            language: '', 
            msg_url_mode: 0,
            ndx_with_packet_id: false, 
            use_shortner_code_as_route_arg: 'NO',
        };
    }          

    #init_packet_container() {
        return {
            recipient: '', 
            message: '', 
            phone: '',
            email: '', 
            full_name: '', 
            generic_name: '', 
            address: '', 
            other_info: '',
            index: '',
        };        
    }

    #set_fixed_parms() {
        for (let key in this.fixed_packet_parms) {
            this.fixed_packet_parms[key] = this.config.get_entry(key);
        }
    }

    #convert(digits_str) {
        let result = '';
        digits_str = digits_str.trim();
        if (digits_str) {
            digits_str.split('').forEach(function(v) {
                if (typeof Packet.char_table[v] !== 'undefined')
                    result += Packet.char_table[v];
                else result += v;
            });
        }
        return result;
    }

    #set_packet_identifier() {
        let recipient = this.packet_container.phone.trim()
            ? this.packet_container.phone : this.packet_container.recipient;
        return this.#convert(recipient);
    }

    #random_str() {
        let arr = new Uint8Array((this.config.get_entry('random_string_length') || 40) / 2);
        window.crypto.getRandomValues(arr);
        return Array.from(arr, dec2hex).join('');
    }

    #get_current_date() {
        const date = new Date();
        return date.toLocaleDateString(); // e.g., 'MM/DD/YYYY'
    }
    
    #get_current_time() {
        const date = new Date();
        return date.toLocaleTimeString(); // e.g., 'HH:MM:SS AM/PM'
    }
    
    #get_day_of_week() {
        const date = new Date();
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return days[date.getDay()];
    }
    
    #get_day_of_year() {
        const date = new Date();
        const start = new Date(date.getFullYear(), 0, 0);
        const diff = date - start;
        const oneDay = 1000 * 60 * 60 * 24;
        return Math.floor(diff / oneDay);
    }
    
    #get_week_of_year() {
        const date = new Date();
        const start = new Date(date.getFullYear(), 0, 1);
        const diff = date - start + ((start.getDay() + 6) % 7) * 86400000;
        return Math.ceil(diff / (7 * 86400000));
    }
    
    #get_month_name() {
        const date = new Date();
        const months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        return months[date.getMonth()];
    }
    
    #get_month_number() {
        const date = new Date();
        return (date.getMonth() + 1).toString().padStart(2, '0'); // e.g., '01' for January
    }
    
    #get_current_year() {
        const date = new Date();
        return date.getFullYear().toString();
    }

    #compose_message() {
        let msg = this.config.get_next_rotation_msg();
        if (msg) {
            let tags = [
                /__FULL_NAME__/g,
                /__PHONE__/g,
                /__RANDOM_STR__/g,
                /__ID__/g,
                /__DATE__/g,
                /__TIME__/g,
                /__DAY_OF_WEEK__/g,
                /__DAY_OF_YEAR__/g,
                /__WEEK_OF_YEAR__/g,
                /__MONTH_NAME__/g,
                /__MONTH_NUMBER__/g,
                /__CURRENT_YEAR__/g
            ];
            let fields = [
                this.packet_container.generic_name,
                this.packet_container.phone,
                this.#random_str(),
                this.#set_packet_identifier(),
                this.#get_current_date(),
                this.#get_current_time(),
                this.#get_day_of_week(),
                this.#get_day_of_year(),
                this.#get_week_of_year(),
                this.#get_month_name(),
                this.#get_month_number(),
                this.#get_current_year()
            ];

            tags.forEach(function(tag, index) {
                msg = msg.replace(tag, fields[index]);
            });
        }
        return msg;
    }

    #set_generic_name() {
        let full_name = this.packet_container.full_name;
        let generic_name = full_name;
        let name_format = this.config.get_entry('name_format');
        let name_parts = full_name.split(' ');
        let nl = name_parts.length;
        name_format = name_format ? name_format : 'FullName';
        if (nl) {
            switch (name_format) {
                case 'FirstName': generic_name = name_parts[0]; break;
                case 'SecondName': generic_name = nl > 1 ? name_parts[1] : name_parts[0]; break;
                case 'LastName': generic_name = name_parts[nl - 1]; break;
                case 'skip1st':
                    name_parts.shift();
                    generic_name = name_parts.length 
                        ? name_parts.join(' ') : full_name;
                    break;             
            }
        }
        return generic_name;
    }

    #set_packet_container(line) {
        const field_separator = ';';
        const line_format = this.config.get_entry('line_format') || 'P-N';
        const pattern = line_format.split('-').join('-');

        const map_to_pattern = (input_line, pattern) => {
            const components = input_line.split(field_separator).map(item => item.trim());
            const keyMap = {
                'P': 'phone',
                'N': 'full_name',
                'E': 'email',
                'A': 'address'
            };
            const patternArray = pattern.split('-');
            const result = {};

            patternArray.forEach((key, index) => {
                if (keyMap[key]) {
                    result[keyMap[key]] = components[index];
                }
            });

            if (components.length > patternArray.length) {
                result.other_info = components.slice(patternArray.length).join('; ');
            }

            return result;
        };

        const parsedData = map_to_pattern(line, pattern);
        this.packet_container = { ...this.packet_container, ...parsedData };
        this.packet_container.generic_name = this.#set_generic_name();
        this.packet_container.recipient = this.packet_container.phone;
        this.packet_container.index = this.config.get_next_index_link() + 
            (this.config.get_entry('index_with_packet_id') ? '/' + this.#set_packet_identifier() : '');
        this.packet_container.message = this.#compose_message();
    }

    assemble(line) {
        line = line.trim();
        if (line) {
            jQuery('.composed-message-container').removeClass('hidden');
            this.#set_packet_container(line);
            return { ...this.fixed_packet_parms, ...this.packet_container, ...this.config.get_entry('settings') };
        }
        return '';
    }

    sequencer(leads) {
        const self = this;
        return function* () {
            let halted;
            for (let i = 0; i < leads.length; i++) {
                halted = yield self.assemble(leads[i]);
                if (halted) return;
            }
        };
    }    
}