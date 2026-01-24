							<td style="padding-left:0;">
								<table>

									<tr>										
										<td style="border-top:none;"><strong>From:</strong></td>
										<td style="border-top:none;">
											<?php
											if(isset($from)){
												$from = \Illuminate\Support\Carbon::parse($from)->format('Y-m-d');
											}else{
												$from = '';
											}
											?>
											<input type="date" name="from" value="{{ $from }}" class="inp" required>
										</td>
										<td style="border-top:none;"><strong>To:</strong></td>
										<td style="border-top:none;">
											<?php
											if(isset($to)){
												$to = \Illuminate\Support\Carbon::parse($to)->format('Y-m-d');
											}else{
												$to = '';
											}
											?>
											<input type="date" name="to" value="{{ $to }}" class="inp" required>
										</td>
									</tr>
								</table>
							</td>
