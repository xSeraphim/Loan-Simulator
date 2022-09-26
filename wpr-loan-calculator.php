<?php
/**
 * Plugin Name: Simulator Imprumuturi
 * Author: Aldea Daniel
 * Version: 1.0.0
 * Description: Acesta este un mic simulator de imprumuturi
 * Text Domain: wpr-loan-calculator
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Plugin URL.
define( 'WPR_CALCULATOR_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
// Plugin path.
define( 'WPR_CALCULATOR_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );



class calculator {

    public function __construct () {
		add_shortcode( 'shortcode_calculator', array($this, 'calculator' ));
		add_action( 'wp_ajax_calculator', array($this,'calculator_callback' ));
		add_action( 'wp_ajax_nopriv_calculator', array($this,'calculator_callback' ));

	}

    private function options() {
        $options = array(
            'none' => array(
                'option' => 'Selecteaza Tipul de Imprumut',
                'min' => 1,
                'max' => 5,
                'suma' => 50000,
            ),
            'bunvenit' => array(
                'option' => 'Imprumutul de Bun Venit',
                'min' => 1,
                'max' => 1,
                'suma' => 5000,
            ),
            'traditional' => array(
                'option' => 'Imprumutul Traditional',
                'min' => 1,
                'max' => 4,
                'suma' => 50000,
            ),
            'optional' => array(
                'option' => 'Imprumutul Optional',
                'min' => 1,
                'max' => 7,
                'suma' => 70000,

            ),
        );
        return $options;

    }
    public function calculator() {
        $this->calculator_scripts();
        
        ob_start();
        // var_dump($this->options());
        ?>

    <div id="wpr-calculator" style="width:100%;">
        <div style="width:100%;" class="custom-select">
            <select id="imprumuturi" name="imprumuturi" class="classic">
                <?php 
                foreach ($this->options() as $key=>$option) {
                    
                    echo '<option data-suma="'. $option['suma'] .'" data-min="'. $option['min'] .'" data-max="'. $option['max'] .'" value="'. $key .'">'. $option['option'] .'</option>';
                }
                ?>
            </select>
        </div>
        <div id="range">
            <label for="suma" style="width:100%;margin-bottom:10px;">Selecteaza suma dorita: <span id="one"></span> lei</label>
            <input type="range" id="suma" name="suma" min="1000" max="50000" step="1000" class="slider" style="margin-bottom:20px;">
            
            <label for="perioada" style="width:100%;margin-bottom:10px;">Selecteaza perioada: <span id="two"></span> an/ani</label>
            <input type="range" id="perioada" name="perioada" min="1" max="5" step="1" class="slider" style="margin-bottom:20px;">
        </div>  
    </div>

    <div id="wpr-calculator-results">
            <p class="loan_text">Rata lunara:</p>
            <p class="loan_text">Dobanda de plata:</p>
            <p class="loan_text">Total de plata:</p>
    </div>


        <?php
        return ob_get_clean();
    }
    public function calculator_callback() {
        header( 'Content-Type: application/json' );
        $imprumuturi = $_GET['imprumuturi'];
        $suma = $_GET['suma'];
        $perioada = $_GET['perioada'];
        $dobTraditional = 0.072;
        $dobOptional = 0.0974;
        $dobBunvenit = 0.087;

        if ($imprumuturi == 'bunvenit') {
            $dobPlata = $dobBunvenit * 100;
            $dobaLunara = ($suma*$dobBunvenit);
        }
        if ($imprumuturi == 'traditional') {
            $dobPlata = $dobTraditional * 100;
            $dobaLunara = ($suma*$dobTraditional);
        }
        else if ($imprumuturi == 'optional') {
            $dobPlata = $dobOptional * 100;
            $dobaLunara = ($suma*$dobOptional);
        }
        
        $totalPlata = $suma+($dobaLunara*$perioada);
        $rataLunara = $totalPlata / ($perioada*12);
        $rezultat = array(
            'rata' => $rataLunara,
            'dobanda' => $dobPlata,
            'total' => $totalPlata,
        );
        echo wp_json_encode($rezultat);
        wp_die();
    }

    public function calculator_scripts() {
        wp_enqueue_script( 'calculator', WPR_CALCULATOR_URL . '/assets/calculator.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script(
			'calculator',
			'WPR',
			array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'calculator' ),
			)
		);
        wp_enqueue_style('calculator_styles', WPR_CALCULATOR_URL . '/assets/calculator.css');
    }

}
new calculator();